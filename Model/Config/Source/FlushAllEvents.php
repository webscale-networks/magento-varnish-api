<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class FlushAllEvents implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @param bool $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->options) {
            $this->options = [
                [
                    'value' => 'clean_media_cache_after',
                    'label' => 'clean_media_cache_after'
                ],
                [
                    'value' => 'clean_catalog_images_cache_after',
                    'label' => 'clean_catalog_images_cache_after'
                ],
                [
                    'value' => 'adminhtml_cache_refresh_type',
                    'label' => 'adminhtml_cache_refresh_type'
                ],
                [
                    'value' => 'adminhtml_cache_flush_all',
                    'label' => 'adminhtml_cache_flush_all'
                ],
                [
                    'value' => 'assign_theme_to_stores_after',
                    'label' => 'assign_theme_to_stores_after'
                ],
            ];
        }

        $options = $this->options;

        if (!$isMultiselect) {
            array_unshift($options, ['value' => '', 'label' => __('--Please Select--')]);
        }

        return $options;
    }
}
