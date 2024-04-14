<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\Import\Behavior;

interface BehaviorProviderInterface
{
    /**
     * @param string $behaviorCode
     *
     * @throws \Amasty\Base\Exceptions\NonExistentImportBehavior
     * @return \Amasty\Base\Model\Import\Behavior\BehaviorInterface
     */
    public function getBehavior($behaviorCode);
}
