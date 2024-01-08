<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Block\System\Config;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Scheduled extends \Webscale\Varnish\Block\System\Config\SettingsAbstract
{
    /**
     * Return header comment part of html for fieldset
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getHeaderCommentHtml($element): string
    {
        return $this->getCacheConfigMessage() . $this->getScheduledConfigMessage();
    }

    /**
     * Check if account and application is configured
     *
     * @return string
     */
    private function getScheduledConfigMessage(): string
    {
        return $this->getMessageWrapper(
            __('Enabling this feature will disable partial cache invalidation.' .
                ' Full varnish cache flush will be executed instead, according to the cron expression configured below.'
            ), 'warning'
        );
    }
}
