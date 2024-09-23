<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Controller\Adminhtml\Purge;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\PageCache\Model\Config as CacheConfig;
use Webscale\Varnish\Controller\Adminhtml\AbstractController;
use Magento\Framework\App\ResponseInterface;

class Url extends AbstractController implements HttpPostActionInterface
{
    const FIELD_NAME_URLS = 'purge_url';
    /**
     * Retrieve accounts
     *
     * @return ResponseInterface
     */
    public function execute(): ResponseInterface
    {
        try {
            $urlsArray = preg_split('/\n|\r\n?/', $this->getRequest()->getParam(self::FIELD_NAME_URLS));

            if ($this->cacheConfig->getType() == CacheConfig::VARNISH && $this->config->isAvailable()) {
                if ($this->purgeCache->sendPurgeRequest([
                    'tagsPattern' => $urlsArray,
                    'event' => 'adminhtml_manual_flush_by_url'
                ])) {
                    $this->messageManager->addSuccessMessage(
                        __('VelocityEDGE cache flushed successfully.')
                    );
                } else {
                    $this->messageManager->addErrorMessage(
                        __('There is error occurred while trying to purge varnish cache.' .
                            ' Please refer to logs for more information.')
                    );
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('There is error occurred while trying to purge varnish cache.' .
                    ' Please refer to logs for more information.')
            );
        }

        return $this->_redirect('adminhtml/cache/index', ['_current' => true]);
    }
}
