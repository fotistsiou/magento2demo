<?php declare(strict_types=1);

namespace Fotistsiou\Blog\ViewModel;

use Fotistsiou\Blog\Api\Data\PostInterface;
use Fotistsiou\Blog\Api\PostRepositoryInterface;
use Fotistsiou\Blog\Model\ResourceModel\Post\Collection;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Post implements ArgumentInterface
{
    public function __construct(
        private Collection $collection,
        private PostRepositoryInterface $postRepository,
        private RequestInterface $request,
    ) {}

    public function getList(): array
    {
        return $this->collection->getItems();
    }

    public function getCount(): int
    {
        return $this->collection->count();
    }

    public function getDetail(): PostInterface
    {
        $id = (int) $this->request->getParam('id');
        return $this->postRepository->getById($id);
    }
}
