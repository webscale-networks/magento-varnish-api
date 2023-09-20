<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Observer;

use Magento\Framework\Event\Observer;
use Magento\PageCache\Model\Config as CacheConfig;
use Webscale\Varnish\Model\PurgeCache;
use Webscale\Varnish\Helper\Config;
use Magento\Framework\Event\ObserverInterface;

class FlushAllCacheObserver implements ObserverInterface
{
    /** @var Config $config */
    private $config;

    /** @var CacheConfig $cacheConfig */
    private $cacheConfig;

    /** @var PurgeCache $purgeCache */
    private $purgeCache;

    /**
     * @param CacheConfig $cacheConfig
     * @param Config $config
     * @param PurgeCache $purgeCache
     */
    public function __construct(
        CacheConfig $cacheConfig,
        Config $config,
        PurgeCache $purgeCache
    ) {
        $this->config = $config;
        $this->cacheConfig = $cacheConfig;
        $this->purgeCache = $purgeCache;
    }

    /**
     * Flush all cache
     *
     * @param Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer): void
    {
        try {
            if ($this->cacheConfig->getType() == CacheConfig::VARNISH && $this->config->isAvailable()) {
                $this->purgeCache->sendPurgeRequest(['tagsPattern' => ['.*']]);
            }
        } catch (\Exception $e) {
            $this->config->log($e->getMessage() . PHP_EOL . $e->getTraceAsString(), 'critical');
        }
    }
}
