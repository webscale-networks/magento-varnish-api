<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Webscale\Varnish\Helper\Config;
use Webscale\Varnish\Service\Api;
use Magento\PageCache\Model\Config as CacheConfig;
use Magento\Store\Model\StoreManagerInterface;
use Webscale\Varnish\Model\PurgeCache;
use Webscale\Varnish\Model\CredentialsManager;

abstract class AbstractController extends Action
{
    /** @var Api $api */
    protected $api;

    /** @var JsonFactory $json */
    protected $json;

    /** @var CacheConfig $cacheConfig */
    protected $cacheConfig;

    /** @var Config $config */
    protected $config;

    /** @var StoreManagerInterface $storeManager */
    protected $storeManager;

    /** @var PurgeCache $purgeCache */
    protected $purgeCache;

    /**
     * @param Context $context
     * @param Api $api
     * @param JsonFactory $json
     * @param Config $config
     * @param CacheConfig $cacheConfig
     * @param StoreManagerInterface $storeManager
     * @param PurgeCache $purgeCache
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function __construct(
        Context $context,
        Api $api,
        JsonFactory $json,
        Config $config,
        CacheConfig $cacheConfig,
        StoreManagerInterface $storeManager,
        PurgeCache $purgeCache
    ) {
        $this->api = $api;
        $this->json = $json;
        $this->config = $config;
        $this->cacheConfig = $cacheConfig;
        $this->storeManager = $storeManager;
        $this->purgeCache = $purgeCache;

        parent::__construct($context);
    }
}
