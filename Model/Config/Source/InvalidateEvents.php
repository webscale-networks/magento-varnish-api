<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class InvalidateEvents implements OptionSourceInterface
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
                    'value' => 'clean_cache_by_tags',
                    'label' => 'clean_cache_by_tags'
                ],
                [
                    'value' => 'assigned_theme_changed',
                    'label' => 'assigned_theme_changed'
                ],
                [
                    'value' => 'catalogrule_after_apply',
                    'label' => 'catalogrule_after_apply'
                ],
                [
                    'value' => 'controller_action_postdispatch_adminhtml_system_currency_saveRates',
                    'label' => 'controller_action_postdispatch_adminhtml_system_currency_saveRates'
                ],
                [
                    'value' => 'controller_action_postdispatch_adminhtml_system_config_save',
                    'label' => 'controller_action_postdispatch_adminhtml_system_config_save'
                ],
                [
                    'value' => 'controller_action_postdispatch_adminhtml_catalog_product_action_attribute_save',
                    'label' => 'controller_action_postdispatch_adminhtml_catalog_product_action_attribute_save'
                ],
                [
                    'value' => 'controller_action_postdispatch_adminhtml_catalog_product_massStatus',
                    'label' => 'controller_action_postdispatch_adminhtml_catalog_product_massStatus'
                ],
                [
                    'value' => 'controller_action_postdispatch_adminhtml_system_currencysymbol_save',
                    'label' => 'controller_action_postdispatch_adminhtml_system_currencysymbol_save'
                ],
                [
                    'value' => 'clean_cache_after_reindex',
                    'label' => 'clean_cache_after_reindex'
                ],
                [
                    'value' => 'adminhtml_cache_flush_system',
                    'label' => 'adminhtml_cache_flush_system'
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
