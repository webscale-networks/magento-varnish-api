<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Block\System\Config;

use Magento\PageCache\Model\Config as CacheConfig;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Settings extends \Webscale\Varnish\Block\System\Config\SettingsAbstract
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
        return $this->getCacheConfigMessage() . $this->getApplicationConfigMessage();
    }

    /**
     * Check if cache config set to varnish
     *
     * @return string
     */
    private function getCacheConfigMessage(): string
    {
        if ($this->cacheConfig->getType() != CacheConfig::VARNISH) {
            $url = $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/system');
            return $this->getMessageWrapper(
                __('Magento is configured to use the built-in Full Page Cache.' .
                    ' To use VelocityEDGE please change "Caching Application" to "Varnish Cache"' .
                    ' under the "Full Page Cache" tab in <a href="%1">System Configuration</a>', $url),
                'error'
            );
        }

        return '';
    }

    /**
     * Check if account and application is configured
     *
     * @return string
     */
    private function getApplicationConfigMessage(): string
    {
        if (empty($this->config->getApiToken())) {
            return $this->getMessageWrapper(
                __('Please configure API Token and Application Id.'),
                'warning'
            );
        }

        if (!$this->config->isAvailable()) {
            return $this->getMessageWrapper(
                __('To be able to use Webscale varnish cache please configure "Application Id".'),
                'warning'
            );
        }

        return '';
    }
}
