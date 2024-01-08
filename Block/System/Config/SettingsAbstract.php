<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Block\System\Config;

use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\View\Helper\Js;
use Magento\PageCache\Model\Config as CacheConfig;
use Webscale\Varnish\Helper\Config;

abstract class SettingsAbstract extends Fieldset
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
        Context      $context,
        Session      $authSession,
        Js           $jsHelper,
        Config       $config,
        UrlInterface $urlBuilder,
        CacheConfig  $cacheConfig
    )
    {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
        $this->cacheConfig = $cacheConfig;

        parent::__construct($context, $authSession, $jsHelper);
    }

    /**
     * Get message wrapper
     *
     * @param string $message
     * @param string $severity
     * @return string
     */
    public function getMessageWrapper(string $message = '', string $severity = 'notice'): string
    {
        $html  = '<div style="padding:10px;"><div class="messages">';
        $html .= '<div class="message message-' . $severity . ' ' . $severity . '" style="margin-bottom: 0;">';
        $html .= '<div data-ui-id="messages-message-' . $severity . '">';
        $html .= $message;
        $html .= '</div></div></div></div>';

        return $html;
    }
}
