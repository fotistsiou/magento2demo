<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\Import\Validation;

interface ValidatorPoolInterface
{
    /**
     * @return \Amasty\Base\Model\Import\Validation\ValidatorInterface[]
     */
    public function getValidators();

    /**
     * @param \Amasty\Base\Model\Import\Validation\ValidatorInterface
     *
     * @return void
     */
    public function addValidator($validator);
}
