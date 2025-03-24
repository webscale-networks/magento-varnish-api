<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Model;

use Magento\Framework\Cache\InvalidateLogger;
use Magento\Framework\Webapi\Rest\Request;
use Webscale\Varnish\Service\Api;
use Webscale\Varnish\Helper\Config;
use Magento\PageCache\Model\Cache\Server;

class PurgeCache
{
    private const BAN_TIMEOUT_SECONDS = 10;

    /** @var InvalidateLogger $logger */
    private $logger;

    /** @var Config $config */
    private $config;

    /** @var Api $api */
    private $api;

    /** @var Server $cacheServer */
    private $cacheServer;

    /**
     * @param InvalidateLogger $logger
     * @param Config $config
     * @param Api $api
     * @param Server $cacheServer
     */
    public function __construct(
        InvalidateLogger $logger,
        Config $config,
        Api $api,
        Server $cacheServer
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->api = $api;
        $this->cacheServer = $cacheServer;
    }

    /**
     * Send curl purge request to invalidate cache by tags pattern
     *
     * @param array $purge
     * @return bool
     */
    public function sendPurgeRequest(array $purge = []): bool
    {
        if (!$this->config->isAvailable()) {
            return false;
        }

        $servers = $this->cacheServer->getUris();

        try {
            $uri = $this->config->getCacheUri();

            $params = $this->config->generateCacheParams($purge);
            $params['event'] = !empty($purge['event']) ? $purge['event'] : '';

            $this->config->log('Purge request: ' . $uri . ' ' . json_encode($params), 'debug');

            $response = $this->api->doRequest($uri, $params, Request::HTTP_METHOD_POST);

            $tags = (!empty($purge['tags']) && is_array($purge['tags']))
                ? implode('|', $purge['tags'])
                : '.*';

            if (!in_array($response->getStatusCode(), [200, 201])) {
                $this->logger->warning(
                    'Error executing purge: ' . $tags . ', Error: ' . $response->getReasonPhrase(),
                    compact('servers', 'tags')
                );
                return false;
            }
        } catch (\Exception $e) {
            $this->logger->critical(
                $e->getMessage(),
                compact('servers', 'tags')
            );
        }

        $this->logger->execute(compact('servers', 'tags'));

        return true;
    }
}
