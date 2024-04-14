<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Controller\Url;

use Amasty\Smtp\Model\UrlBuilder\GenerateAuthorizationUrl;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\ValidatorException;

class Build extends Action
{
    /**
     * @var GenerateAuthorizationUrl
     */
    private $generateAuthorizationUrl;

    public function __construct(
        GenerateAuthorizationUrl $generateAuthorizationUrl,
        Context $context
    ) {
        $this->generateAuthorizationUrl = $generateAuthorizationUrl;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws ValidatorException
     */
    public function execute()
    {
        $microsoftAuthUrl = $this->generateAuthorizationUrl->getMicrosoftAuthUrl($this->getRequest()->getParams());

        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $result->setUrl($microsoftAuthUrl);
    }
}
