<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Block\System\Config;

use Webscale\Varnish\Helper\Config;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\View\Helper\Js;
use Magento\Backend\Model\UrlInterface;
use Magento\PageCache\Model\Config as CacheConfig;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Settings extends Fieldset
{
    /**
     * @var Config $config
     */
    protected $config;

    /**
     * @param Context $context
     * @param Session $authSession
     * @param Js $jsHelper
     * @param Config $config
     * @param UrlInterface $urlBuilder
     * @param CacheConfig $cacheConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        Config $config,
        UrlInterface $urlBuilder,
        CacheConfig $cacheConfig,
        array $data = []
    ) {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
        $this->cacheConfig = $cacheConfig;

        parent::__construct($context, $authSession, $jsHelper, $data);
    }

    /**
     * Return header comment part of html for fieldset
     *
     * @param AbstractElement $element
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
                    ' To use Webscale varnish caching please change "Caching Application" to "Varnish Cache"' .
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

    /**
     * Get message wrapper
     *
     * @param string $message
     * @param string $severity
     * @return string
     */
    private function getMessageWrapper(string $message = '', string $severity = 'notice'): string
    {
        $html  = '<div style="padding:10px;"><div class="messages">';
        $html .= '<div class="message message-' . $severity . ' ' . $severity . '" style="margin-bottom: 0;">';
        $html .= '<div data-ui-id="messages-message-' . $severity . '">';
        $html .= $message;
        $html .= '</div></div></div></div>';

        return $html;
    }
}
