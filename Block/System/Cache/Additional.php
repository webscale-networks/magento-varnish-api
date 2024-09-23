<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Block\System\Cache;

use Magento\Backend\Block\Template;
use Magento\PageCache\Model\Config as CacheConfig;
use Webscale\Varnish\Helper\Config;

class Additional extends Template
{
    /** @var Config $config */
    private $config;

    /** @var CacheConfig $cacheConfig */
    private $cacheConfig;

    /**
     * @param Template\Context $context
     * @param Config $config
     * @param CacheConfig $cacheConfig
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        CacheConfig $cacheConfig
    ) {
        $this->config = $config;
        $this->cacheConfig = $cacheConfig;

        parent::__construct($context);
    }

    /**
     * Retrieve purge cache URL
     *
     * @return string
     */
    public function getPurgeAllUrl(): string
    {
        return $this->getUrl('webscalevarnish/purge/all', ['_current' => true]);
    }

    public function getPurgeByUrlUrl(): string
    {
        return $this->getUrl('webscalevarnish/purge/url', ['_current' => true]);
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return ($this->cacheConfig->getType() == CacheConfig::VARNISH && $this->config->isAvailable());
    }
}
