<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Console\Command;

use Amasty\Base\Model\SysInfo\Command\LicenceService\RegisterLicenceKey as CommandRegisterLicenceKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterLicenceKey extends Command
{
    /**
     * @var CommandRegisterLicenceKey
     */
    private $registerLicenceKey;

    public function __construct(
        CommandRegisterLicenceKey $registerLicenceKey,
        string $name = null
    ) {
        parent::__construct($name);
        $this->registerLicenceKey = $registerLicenceKey;
    }

    protected function configure()
    {
        $this->setName('amasty-base:licence:register-key');
        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->registerLicenceKey->execute();

        return 0;
    }
}
