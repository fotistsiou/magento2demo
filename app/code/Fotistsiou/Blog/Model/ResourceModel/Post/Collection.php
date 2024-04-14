<?php declare(strict_types=1);

namespace Fotistsiou\Blog\Model\ResourceModel\Post;

use Fotistsiou\Blog\Model\Post;
use Fotistsiou\Blog\Model\ResourceModel\Post as PostResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Post::class, PostResource::class);
    }
}
