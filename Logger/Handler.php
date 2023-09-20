<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Logger;

use Monolog\Logger;
use Magento\Framework\Logger\Handler\Base;

class Handler extends Base
{
    /** @var int $loggerType */
    protected $loggerType = Logger::INFO;

    /** @var string $fileName */
    protected $fileName = '/var/log/webscale.log';
}
