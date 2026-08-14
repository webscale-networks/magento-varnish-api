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
        $tags = $this->getTagsPattern($purge);

        try {
            $uri = $this->config->getCacheUri();

            $params = $this->config->generateCacheParams($purge);
            $params['event'] = !empty($purge['event']) ? $purge['event'] : '';

            $this->config->log('Purge request: ' . $uri . ' ' . json_encode($params), 'debug');

            $response = $this->api->doRequest($uri, $params, Request::HTTP_METHOD_POST);

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
            return false;
        }

        $this->logger->execute(compact('servers', 'tags'));

        return true;
    }

    /**
     * Build the tags pattern used for logging, tolerating non-string tag values
     *
     * @param array $purge
     * @return string
     */
    private function getTagsPattern(array $purge): string
    {
        $tags = [];

        if (!empty($purge['tags']) && is_array($purge['tags'])) {
            foreach ($purge['tags'] as $tag) {
                if (is_scalar($tag)) {
                    $tags[] = (string) $tag;
                }
            }
        }

        return empty($tags) ? '.*' : implode('|', $tags);
    }
}
