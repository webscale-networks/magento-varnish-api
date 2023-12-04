<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Helper;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\ModuleListInterface;
use Webscale\Varnish\Service\Api;
use Webscale\Varnish\Logger\Logger;

class Config extends AbstractHelper
{
    public const XML_PATH_ENABLED = 'webscale_varnish/general/enabled';

    public const XML_PATH_TOKEN = 'webscale_varnish/application/token';

    public const XML_PATH_APPLICATION = 'webscale_varnish/application/application_id';

    public const XML_PATH_DEBUG = 'webscale_varnish/developer/debug';

    public const XML_PATH_EVENTS_ALL = 'webscale_varnish/cache_events/flush_all_events';

    public const XML_PATH_EVENTS_PARTIAL = 'webscale_varnish/cache_events/partial_invalidate_events';


    /** @var ModuleListInterface $moduleList */
    private $moduleList;

    /** @var Logger $logger */
    protected $logger;

    /**
     * @param Context $context
     * @param ModuleListInterface $moduleList
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        ModuleListInterface $moduleList,
        WriterInterface $writerInterface,
        Logger $logger
    ) {
        $this->moduleList = $moduleList;
        $this->writerInterface = $writerInterface;
        $this->logger = $logger;

        parent::__construct($context);
    }

    /**
     * Check if integration is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_ENABLED);
    }

    /**
     * Check if module setup
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return (
            $this->isEnabled()
            && $this->getApplicationId()
            && !empty($this->getApiToken())
        );
    }

    /**
     * Retrieve debug settings
     *
     * @return bool
     */
    public function debugEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_DEBUG);
    }

    /**
     * Retrieve API token
     *
     * @return string
     */
    public function getApiToken(): string
    {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_TOKEN);
    }

    /**
     * Retrieve application id
     *
     * @return string
     */
    public function getApplicationId(): string
    {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_APPLICATION);
    }

    /**
     * Retrieve flush all events array
     *
     * @return array
     */
    public function getEventsFlushAll()
    {
        $events = $this->scopeConfig->getValue(self::XML_PATH_EVENTS_ALL);

        return is_array($events) ? $events : explode(',', $events);
    }

    /**
     * Retrieve partial invalidate events array
     *
     * @return array
     */
    public function getEventsPartialInvalidate()
    {
        $events = $this->scopeConfig->getValue(self::XML_PATH_EVENTS_PARTIAL);

        return is_array($events) ? $events : explode(',', $events);
    }

    /**
     * Prepare service URL
     *
     * @return string
     */
    public function getCacheUri()
    {
        return '/v2/tasks';
    }

    /**
     * Prepare request params
     *
     * @param array $purge
     * @return array
     */
    public function generateCacheParams(array $purge = []) {
        $params = [
            'json' => [
                'type' => 'invalidate-cache',
                'target' => '/v2/applications/' . $this->getApplicationId(),
                'parameters' => [
                    'urls' => ['*://*/*'],
                    'tags' => !empty($purge['tagsPattern']) ? $purge['tagsPattern'] : ['.*'],
                ]
            ],
        ];

        return $params;
    }

    /**
     * Retrieve module version
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getVersion(): string
    {
        try {
            $name = parent::_getModuleName();
            $version = $this->moduleList->getOne($name)['setup_version'];
        } catch (\Exception $e) {
            $version = '0.0.1';
        }

        return $version;
    }

    /**
     * Write message to custom log
     *
     * @param string|array|object $message
     * @param string $level
     * @return bool
     */
    public function log($message, string $level = 'info'): bool
    {
        if (!$this->debugEnabled() && $level == 'info') {
            return false;
        }

        if (is_array($message) || is_object($message)) {
            $message = json_encode($message);
        }

        $this->logger->log($level, $message);

        return true;
    }
}
