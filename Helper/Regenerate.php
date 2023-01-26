<?php

namespace HimaMage\RegenerateUrlRewrites\Helper;
/**
 * Constructor modification point for Magento\Framework\App\Helper.
 * All context classes were introduced to allow for backwards compatible constructor modifications of classes
 * that were supposed to be extended by extension developers.
 *
 */
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface; // store manager
/**
 * every config have value and flag
 * can be get both using path and scope
 */
use Magento\Framework\App\Config\ScopeConfigInterface;

class Regenerate
{
    /**
     * @var StoreManagerInterface
     */
    protected  $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->scopeConfig = $context->getScopeConfig();

    }

    /**
     * Get Store Manager : getter
     * @return StoreManagerInterface
     */
    public function getStoreManager(): StoreManagerInterface
    {
        return $this->storeManager;
    }

    /**
     * this will get the config value for using categories path for product Urls
     * @param $storeId
     * @return bool
     */
    public function useCategoriesPathForProductUrls($storeId = null)
    {
        // cast it to boolean
        return (bool) $this->scopeConfig->getValue(
            'catalog/seo/product_use_categories',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $productUrlRewrites
     * @return mixed
     */
    public function sanitizeProductUrlRewrites($productUrlRewrites)
    {
        $paths = [];
        foreach ($productUrlRewrites as $key => $urlRewrite) {
            $path = $this->_clearRequestPath($urlRewrite->getRequestPath());
            if (!in_array($path , $paths)) {
                $productUrlRewrites[$key]->setRequestPath($path);
                $paths[] = $path;
            } else {
                // unset() destroys the specified variables
                unset($productUrlRewrites[$key]);
            }
        }
        return $productUrlRewrites;

    }

    /**
     * @param $requestPath
     * @return array|string|string[]
     */
    public function _clearRequestPath($requestPath)
    {
        /**
         * This function returns a string or an array with all occurrences of search in subject replaced with the given replace value
         * str_replace(
            array|string $search,
            array|string $replace,
            string|array $subject,
            int &$count = null
            ): string|array
         * ltrim — Strip whitespace (or other characters) from the beginning of a string
         * ltrim(string $string, string $characters = " \n\r\t\v\x00"): string
         */
        return str_replace(
            ['//' , './' ], // search
            ['/' , '/'], // replace

            ltrim(
                ltrim($requestPath , '/'),
                '.'
            ) // subject
        );

    }
}
