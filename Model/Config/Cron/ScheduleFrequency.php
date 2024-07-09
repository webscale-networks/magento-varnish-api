<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Model\Config\Cron;

use Magento\Framework\Model\ResourceModel\AbstractResource;
use Webscale\Varnish\Model\Config\Source\Cron\Frequency;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Model\Context;
use Webscale\Varnish\Helper\Config;
use Magento\Framework\Registry;

class ScheduleFrequency extends Value
{
    const CRON_STRING_PATH = 'crontab/default/jobs/webscale_varnish_cache_flush_scheduled/schedule/cron_expr';

    const CRON_MODEL_PATH = 'crontab/default/jobs/webscale_varnish_cache_flush_scheduled/run/model';

    /**
     * @var ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @var string
     */
    protected $_runModelPath = '';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param ValueFactory $configValueFactory
     * @param Config $config
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param string $runModelPath
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $scopeConfig,
        TypeListInterface $cacheTypeList,
        ValueFactory $configValueFactory,
        Config $config,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        string $runModelPath = '',
        array $data = []
    ) {
        $this->_runModelPath = $runModelPath;
        $this->_configValueFactory = $configValueFactory;
        $this->config = $config;
        parent::__construct($context, $registry, $scopeConfig, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return ScheduleFrequency
     * @throws \Exception
     */
    public function afterSave()
    {
        $time = $this->getData('groups/flush_cache_schedule/fields/start_time/value');
        $frequency = $this->getData('groups/flush_cache_schedule/fields/frequency/value');
        $every = $this->getData('groups/flush_cache_schedule/fields/every/value');
        $startingAt = $this->getData('groups/flush_cache_schedule/fields/starting_at/value');

        $cronExprArray = $this->getCronExpressionArray($frequency, $every, $time, $startingAt);
        $cronExprString = join(' ', $cronExprArray);

        try {
            $this->_configValueFactory->create()->load(
                self::CRON_STRING_PATH,
                'path'
            )->setValue(
                $cronExprString
            )->setPath(
                self::CRON_STRING_PATH
            )->save();

            $this->_configValueFactory->create()->load(
                self::CRON_MODEL_PATH,
                'path'
            )->setValue(
                $this->_runModelPath
            )->setPath(
                self::CRON_MODEL_PATH
            )->save();
        } catch (\Exception $e) {
            throw new \Exception(__('Can\'t save the cron expression.'));
        }

        return parent::afterSave();
    }

    /**
     * @param string $frequency
     * @return array
     */
    protected function getCronExpressionArray($frequency = '', $every = '', $time = [], $startingAt = null)
    {
        switch ($frequency) {
            case Frequency::CRON_HOURLY:
                $cronExprArray = $this->config->getCronExpressionByValue(Frequency::CRON_HOURLY);
                break;
            case Frequency::CRON_DAILY:
                $cronExprArray = $this->config->getCronExpressionByValue(Frequency::CRON_DAILY, [
                    'hours' => intval($time[0]),
                    'minutes' => intval($time[1])
                ]);
                break;
            case Frequency::CRON_CUSTOM:
                $cronExprArray = $this->config->getCronExpressionByValue(Frequency::CRON_CUSTOM, [
                    'every' => $every,
                    'starting_at' => $startingAt
                ]);
                break;
            default:
                $cronExprArray = [];
                break;
        }

        return $cronExprArray;
    }
}
