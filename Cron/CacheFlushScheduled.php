<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Cron;

use Webscale\Varnish\Helper\Config;
use Magento\PageCache\Model\Config as CacheConfig;
use Webscale\Varnish\Model\PurgeCache;

class CacheFlushScheduled
{
    const EVENT_NAME = 'webscale_varnish_flush_all_scheduled';

    /** @var Config $config */
    private $config;

    /** @var CacheConfig $cacheConfig */
    private $cacheConfig;

    /** @var PurgeCache $purgeCache */
    private $purgeCache;

    /**
     * @param Config $config
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
     * @return void
     */
    public function execute() {
        try {
            if (
                $this->cacheConfig->getType() == CacheConfig::VARNISH
                && $this->config->isAvailable()
                && !empty($this->config->getCronExpression())
            ) {
                $this->purgeCache->sendPurgeRequest(['tags' => ['.*'], 'event' => self::EVENT_NAME]);
                $this->config->log('Executed scheduled varnish cache flush.');
            }
        } catch (\Exception $e) {
            $this->config->log('Unable to execute scheduled varnish cache flush.');
            $this->config->log($e->getMessage() . PHP_EOL . $e->getTraceAsString(), 'critical');
        }

    }
}
