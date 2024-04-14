<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SMTP Email Settings for Magento 2
 */

namespace Amasty\Smtp\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;

class Config extends ConfigProviderAbstract
{
    /**
     * @deprecated Use \Amasty\Smtp\Model\Config::$pathPrefix and constant to setting
     */
    public const CONFIG_PATH_GENERAL_CONFIG = 'amsmtp/general/';
    /**
     * @deprecated Use \Amasty\Smtp\Model\Config::$pathPrefix and constant to setting
     */
    public const CONFIG_PATH_SMTP_CONFIG = 'amsmtp/smtp/';

    /**
     * @var string
     */
    protected $pathPrefix = 'amsmtp/';

    /**#@+
     * Constants defined for xpath of system configuration
     */
    public const OAUTH_MICROSOFT_SENDER_MAIL = 'smtp/oauth2_microsoft_sender_email';
    public const OAUTH_MICROSOFT_CLIENT_ID = 'smtp/oauth2_microsoft_client_id';
    public const OAUTH_MICROSOFT_TENANT_ID = 'smtp/oauth2_microsoft_tenant_id';
    public const OAUTH_MICROSOFT_CLIENT_SECRET = 'smtp/oauth2_microsoft_client_secret';
    public const OAUTH_MICROSOFT_INTERNAL_TOKEN = 'smtp/oauth2_microsoft_internal_token';
    public const OAUTH_MICROSOFT_REFRESH_TOKEN = 'smtp/oauth2_microsoft_refresh_token';
    /**#@-*/

    public const CONFIG_PATH_SMTP_BLACKLIST_ENABLE = 'blacklist/enable';

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var ReinitableConfigInterface
     */
    private $reinitableConfig;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        WriterInterface $configWriter,
        ReinitableConfigInterface $reinitableConfig
    ) {
        parent::__construct($scopeConfig);
        $this->encryptor = $encryptor;
        $this->configWriter = $configWriter;
        $this->reinitableConfig = $reinitableConfig;
    }

    /**
     * @param $path
     * @param null $storeId
     * @param string $scope
     *
     * @return mixed
     */
    public function getConfigValueByPath(
        $path,
        $storeId = null,
        $scope = ScopeInterface::SCOPE_STORE
    ) {
        return $this->scopeConfig->getValue($path, $scope, $storeId);
    }

    /**
     * @param int|string $storeId
     * @return mixed
     */
    public function isSmtpEnable($storeId)
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_GENERAL_CONFIG . 'enable',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|string $storeId
     *
     * @return bool
     */
    public function getDisableDelivery($storeId)
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_GENERAL_CONFIG . 'disable_delivery',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|string $storeId
     * @param string $scope
     *
     * @return bool
     */
    public function getUseCustomSender($storeId, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_SMTP_CONFIG . 'use_custom_email_sender',
            $scope,
            $storeId
        );
    }

    /**
     * @param $storeId
     * @param string $scope
     *
     * @return array
     */
    public function getCustomSender($storeId, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        $data = [];
        $data['email'] = $this->scopeConfig->getValue(
            self::CONFIG_PATH_SMTP_CONFIG . 'custom_sender_email',
            $scope,
            $storeId
        );
        $data['name'] = $this->scopeConfig->getValue(
            self::CONFIG_PATH_SMTP_CONFIG . 'custom_sender_name',
            $scope,
            $storeId
        );

        return $data['email'] && $data['name'] ? $data : [];
    }

    /**
     * @param $storeId
     * @param string $scope
     *
     * @return array
     */
    public function getSmtpConfig($storeId, $scope = ScopeInterface::SCOPE_STORE)
    {
        $result = [];
        $config = $this->getConfigValueByPath(
            trim(self::CONFIG_PATH_SMTP_CONFIG, '/'),
            $storeId,
            $scope
        );
        $result['host'] = $config['server'] ?? '';
        $result['provider'] = $config['provider'];
        $result['parameters'] = [
            'port' => $config['port'],
            'auth' => $config['auth'] ?? '',
            'ssl'  => $config['sec'] ?? '',
        ];

        if (isset($config['login'], $config['passw'])) {
            $result['parameters']['username'] = trim($config['login']);
            $result['parameters']['password'] = $this->encryptor->decrypt($config['passw']);
        }

        if ($config['provider'] == 0
            && !empty($config['use_custom_email_sender'])
        ) {
            $result['parameters']['custom_sender'] = [
                'email' => $config['custom_sender_email'] ?? '',
                'name' => $config['custom_sender_name'] ?? '',
            ];
        }

        if (!$result['parameters']['ssl']) {
            unset($result['parameters']['ssl']);
        }

        $result['test_email'] = $config['test_email'] ?? '';

        return $result;
    }

    /**
     * @param $storeId
     * @param string $scope
     *
     * @return mixed
     */
    public function getGeneralEmail($storeId, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue('trans_email/ident_general', $scope, $storeId);
    }

    public function getGoogleServiceGSuiteUserEmail(): string
    {
        return (string)$this->scopeConfig->getValue(self::CONFIG_PATH_SMTP_CONFIG . 'xoauth2_google_service_user');
    }

    public function getGoogleServiceEmail(): string
    {
        return (string)$this->scopeConfig->getValue(self::CONFIG_PATH_SMTP_CONFIG . 'xoauth2_google_service_email');
    }

    public function getGoogleServiceCredentialsJson(): string
    {
        return $this->encryptor->decrypt(
            $this->scopeConfig->getValue(self::CONFIG_PATH_SMTP_CONFIG . 'xoauth2_google_credentials')
        );
    }

    public function getGoogleClientEmail(): string
    {
        return (string)$this->scopeConfig->getValue(self::CONFIG_PATH_SMTP_CONFIG . 'xoauth2_google_client_email');
    }

    public function getGoogleClientId(): string
    {
        return (string)$this->scopeConfig->getValue(self::CONFIG_PATH_SMTP_CONFIG . 'xoauth2_google_client_id');
    }

    public function getGoogleClientSecret(): string
    {
        return $this->encryptor->decrypt(
            $this->scopeConfig->getValue(self::CONFIG_PATH_SMTP_CONFIG . 'xoauth2_google_client_secret')
        );
    }

    public function getGoogleClientRefreshToken(): string
    {
        return $this->encryptor->decrypt(
            $this->scopeConfig->getValue(self::CONFIG_PATH_SMTP_CONFIG . 'xoauth2_google_client_refresh_token')
        );
    }

    public function getMicrosoftSenderEmail(): string
    {
        return (string)$this->getValue(self::OAUTH_MICROSOFT_SENDER_MAIL);
    }

    public function getMicrosoftClientId(): string
    {
        return (string)$this->getValue(self::OAUTH_MICROSOFT_CLIENT_ID);
    }

    public function getMicrosoftTenantId(): string
    {
        return (string)$this->getValue(self::OAUTH_MICROSOFT_TENANT_ID);
    }

    public function getMicrosoftClientSecret(): string
    {
        return $this->encryptor->decrypt(
            $this->getValue(self::OAUTH_MICROSOFT_CLIENT_SECRET)
        );
    }

    public function getInternalToken(bool $needGenerate = false): string
    {
        $token = $this->getValue(self::OAUTH_MICROSOFT_INTERNAL_TOKEN);
        if ($needGenerate) {
            $token = hash('sha256', rand(1, PHP_INT_MAX));
            $this->getConfigWriter()->save(
                $this->pathPrefix . self::OAUTH_MICROSOFT_INTERNAL_TOKEN,
                $token
            );
            $this->getReInitConfig()->reinit();
        }

        return $token;
    }

    public function setRefreshToken(string $token): void
    {
        $this->getConfigWriter()->save(
            $this->pathPrefix . self::OAUTH_MICROSOFT_REFRESH_TOKEN,
            $this->encryptor->encrypt($token)
        );
        $this->getReInitConfig()->reinit();
    }

    public function getRefreshToken(): string
    {
        return $this->encryptor->decrypt(
            $this->getValue(self::OAUTH_MICROSOFT_REFRESH_TOKEN)
        );
    }

    public function deleteRefreshToken(): void
    {
        $this->getConfigWriter()->delete(
            $this->pathPrefix . self::OAUTH_MICROSOFT_REFRESH_TOKEN
        );
        $this->getReInitConfig()->reinit();
    }

    private function getConfigWriter(): WriterInterface
    {
        return $this->configWriter;
    }

    private function getReInitConfig(): ReinitableConfigInterface
    {
        return $this->reinitableConfig;
    }

    public function isBlacklistEnable(): bool
    {
        return $this->isSetFlag(self::CONFIG_PATH_SMTP_BLACKLIST_ENABLE);
    }

    public function getSmtpLogin(): string
    {
        return (string)$this->scopeConfig->getValue(self::CONFIG_PATH_SMTP_CONFIG . 'login');
    }
}
