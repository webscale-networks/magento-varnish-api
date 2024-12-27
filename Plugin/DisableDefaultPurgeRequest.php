<?php
declare(strict_types=1);

namespace Webscale\Varnish\Plugin;

use Magento\CacheInvalidate\Model\PurgeCache;
use Webscale\Varnish\Helper\Config;

class DisableDefaultPurgeRequest
{
    /** @var Config $config */

    private $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param PurgeCache $subject
     * @param callable $proceed
     * @param array|string $tags
     * @return bool
     */
    public function aroundSendPurgeRequest(PurgeCache $subject, callable $proceed, array|string $tags): bool
    {
        if ($this->config->isAvailable()) {
            return true;
        }
        return $proceed($tags);
    }
}
