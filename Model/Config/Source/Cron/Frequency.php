<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Model\Config\Source\Cron;

use Magento\Framework\Data\OptionSourceInterface;

class Frequency implements OptionSourceInterface
{
    const CRON_DISABLED = '';

    const CRON_HOURLY = 'H';

    const CRON_DAILY = 'D';

    const CRON_CUSTOM = 'custom';

    /**
     * @var array
     */
    protected static $options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!self::$options) {
            self::$options = [
                ['label' => __('Disabled'), 'value' => static::CRON_DISABLED],
                ['label' => __('Hourly'), 'value' => static::CRON_HOURLY],
                ['label' => __('Daily'), 'value' => static::CRON_DAILY],
                ['label' => __('Custom'), 'value' => static::CRON_CUSTOM],
            ];
        }
        return self::$options;
    }
}
