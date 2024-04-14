<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Controller\Oauth;

use Amasty\Base\Model\Serializer;
use Amasty\Smtp\Block\Adminhtml\System\Config\Oauth\Result;
use Amasty\Smtp\Controller\Adminhtml\Config\Check;
use Amasty\Smtp\Model\Config;
use Amasty\Smtp\Model\Token\Generator\Microsoft\ClientIdAccessTokenGenerator;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Block\Template;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\LayoutInterface;
use Psr\Log\LoggerInterface;

class Callback extends Action
{
    public const AUTH_STATE_STRING = 'state';

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var Check
     */
    private $configCheckAndSave;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string|null
     */
    private $exceptionMessage = null;

    public function __construct(
        Serializer $serializer,
        Context $context,
        Config $configProvider,
        Check $configCheckAndSave,
        LayoutInterface $layout,
        LoggerInterface $logger
    ) {
        $this->serializer = $serializer;
        $this->configProvider = $configProvider;
        $this->configCheckAndSave = $configCheckAndSave;
        $this->layout = $layout;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute(): Raw
    {
        try {
            $this->checkCodeParam();
            $this->addStoreAndWebsiteIdsToParams($this->processStateParam());
            $this->configCheckAndSave->execute();
        } catch (\Exception $exception) {
            $this->exceptionMessage = $exception->getMessage();
            $this->logger->error(
                sprintf(
                    '[Amasty_Smtp - Microsoft OAUTH2]: Cannot send test email due to the error: %s.',
                    $this->exceptionMessage
                ),
                $exception->getTrace()
            );
        }

        /** @var Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setHeader(
            'Cache-Control',
            'no-store, no-cache, must-revalidate, max-age=0',
            true
        );

        return $resultRaw->setContents(
            $this->getResultText()
        );
    }

    private function getResultText(): string
    {
        /** @var Template $resultBlock */
        $resultBlock = $this->layout->createBlock(Result::class, '', [
            'data' => [
                'message' => $this->exceptionMessage
            ]
        ]);

        return $resultBlock->toHtml();
    }

    /**
     * @throws LocalizedException
     */
    private function checkCodeParam(): void
    {
        if (!$this->getRequest()->getParam(ClientIdAccessTokenGenerator::AUTH_CODE)) {
            throw new LocalizedException(__('Invalid authentication code'));
        }
    }

    /**
     * @throws LocalizedException
     */
    private function processStateParam(): array
    {
        if (null === ($state = $this->getRequest()->getParam(self::AUTH_STATE_STRING))) {
            throw new LocalizedException(__('Invalid state string'));
        }

        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $unserializedParams = $this->serializer->unserialize(base64_decode($state));
        if ($unserializedParams['internal_token'] !== $this->configProvider->getInternalToken()) {
            throw new LocalizedException(__('Invalid internal token'));
        }

        return $unserializedParams;
    }

    private function addStoreAndWebsiteIdsToParams(array $ids): void
    {
        $this->getRequest()->setParams($ids);
    }
}
