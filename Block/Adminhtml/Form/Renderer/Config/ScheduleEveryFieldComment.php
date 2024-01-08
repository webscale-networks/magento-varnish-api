<?php
/**
 * Copyright © Webscale. All rights reserved.
 * See LICENSE for license details.
 */

namespace Webscale\Varnish\Block\Adminhtml\Form\Renderer\Config;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Config\Model\Config\CommentInterface;
use Magento\Framework\View\Element\Context;
use Webscale\Varnish\Helper\Config;

class ScheduleEveryFieldComment extends AbstractBlock implements CommentInterface
{
    /**
     * @param Context $context
     * @param array $data
     * @param Config $config
     */
    public function __construct(
        Context $context,
        Config $config
    ) {
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * @param $elementValue
     * @return string
     */
    public function getCommentText($elementValue)
    {
        return 'Cron Expression: <span style="color:grey;">none</span>';
    }
}
