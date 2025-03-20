<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Helper;

use Webscale\Varnish\Model\Config\Source\Cron\Frequency;
use Webscale\Varnish\Model\Config\Cron\ScheduleFrequency;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\ModuleListInterface;
use Webscale\Varnish\Logger\Logger;

class Config extends AbstractHelper
{
    public const XML_PATH_ENABLED = 'webscale_varnish/general/enabled';

    public const XML_PATH_TOKEN = 'webscale_varnish/application/token';

    public const XML_PATH_APPLICATION = 'webscale_varnish/application/application_id';

    public const XML_PATH_DEBUG = 'webscale_varnish/developer/debug';

    public const XML_PATH_EVENTS_ALL = 'webscale_varnish/cache_events/flush_all_events';

    public const XML_PATH_EVENTS_PARTIAL = 'webscale_varnish/cache_events/partial_invalidate_events';

    public const XML_PATH_CACHE_SCHEDULE_EVERY = 'webscale_varnish/flush_cache_schedule/every';

    public const DEFAULT_CRON_EXPRESSION = ['*', '*', '*', '*', '*'];

    /** @var ModuleListInterface $moduleList */
    private $moduleList;

    /** @var Logger $logger */
    protected $logger;

    /** @var WriterInterface $writerInterface */
    protected $writerInterface;

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
     * Retrieve cache scheduled every
     *
     * @return array
     */
    public function getCacheScheduleEvery(): array
    {
        return $this->getArrayValue(self::XML_PATH_CACHE_SCHEDULE_EVERY);
    }

    /**
     * Retrieve current value for cron expression
     *
     * @return string
     */
    public function getCronExpression(): string
    {
        return (string) $this->scopeConfig->getValue(ScheduleFrequency::CRON_STRING_PATH);
    }

    /**
     * Retrieve flush all events array
     *
     * @return array
     */
    public function getEventsFlushAll(): array
    {
        return $this->getArrayValue(self::XML_PATH_EVENTS_ALL);
    }

    /**
     * Retrieve partial invalidate events array
     *
     * @return array
     */
    public function getEventsPartialInvalidate(): array
    {
        return $this->getArrayValue(self::XML_PATH_EVENTS_PARTIAL);
    }

    /**
     * Prepare service URL
     *
     * @return string
     */
    public function getCacheUri(): string
    {
        return '/v2/tasks';
    }

    /**
     * Prepare request params
     *
     * @param array $purge
     * @return array
     */
    public function generateCacheParams(array $purge = []): array
    {
        $params = [
            'json' => [
                'type' => 'invalidate-cache',
                'target' => '/v2/applications/' . $this->getApplicationId(),
                'parameters' => []
            ],
        ];

        if(!empty($purge['tagsPattern'])) {
            $params['json']['parameters']['tags'] = $purge['tagsPattern'];
        }
        if(!empty($purge['urls'])) {
            $params['json']['parameters']['urls'] = $purge['urls'];
        }

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
     * @param string $value
     * @return string
     */
    public function getCronExpressionByValue($value, $data = ['every' => '', 'hours' => 0, 'minutes' => 0])
    {
        switch($value) {
            case Frequency::CRON_HOURLY:
                $result = array_replace(self::DEFAULT_CRON_EXPRESSION, ['0']);
                break;
            case Frequency::CRON_DAILY:
                $result = array_replace( self::DEFAULT_CRON_EXPRESSION, [$data['minutes'], $data['hours']]);
                break;
            case Frequency::CRON_CUSTOM:
                $result = $this->getCustomCronExpression($data['every']);
                break;
            default:
                $result = [];
        }

        return $result;
    }

    /**
     * @param array $every
     * @return array
     */
    private function getCustomCronExpression($every = []): array
    {
        $result = [];

        if (is_array($every) && !empty($every[0]) && !empty($every[1])) {
            if ($every[1]  == 'hour') {
                $result = array_replace( self::DEFAULT_CRON_EXPRESSION, ['0', '*/' . $every[0]]);
            } else if ($every[1] == 'min') {
                $result = array_replace( self::DEFAULT_CRON_EXPRESSION, ['*/' . $every[0]]);
            }
        }

        return $result;
    }

    /**
     * Retrieve multiselect values from store config
     *
     * @param string $path
     * @return array
     */
    public function getArrayValue(string $path): array
    {
        $value = $this->scopeConfig->getValue($path);
        if (!empty($value)) {
            return is_array($value) ? $value : explode(',', $value);
        }

        return [];
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
