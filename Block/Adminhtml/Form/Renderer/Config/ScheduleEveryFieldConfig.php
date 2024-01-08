<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Block\Adminhtml\Form\Renderer\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Select;

class ScheduleEveryFieldConfig extends Field
{
    public function __construct(
        Context $context,
        Select $select
    ) {
        $this->select = $select;

        parent::__construct($context);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element) {
        $name = $element->getName();

        $element->setStyle('width: 100px; margin-right: 20px;')->setName($name . '[]');

        if ($element->getValue()) {
            $values = explode(',', $element->getValue());
        } else {
            $values = [];
        }

        $field = $element->setValue(isset($values[0]) ? $values[0] : null)->getElementHtml();

        $units = $this->select->setName($name . '[]')
            ->setStyle('width: 142px;')
            ->setForm($element->getForm())
            ->setValues([
                ['value' => 'min', 'label' => 'minute(s)'],
                ['value' => 'hour', 'label' => 'hour(s)']
            ])
            ->setId('webscale_varnish_flush_cache_schedule_time')
            ->setValue(isset($values[1]) ? $values[1] : null)
            ->getElementHtml();

        return '<div style="float: left;">' . $field . '</div>'
             . '<div style="float: left;">' . $units . '</div>'
             . '<div style="clear: both"></div>';
    }
}
