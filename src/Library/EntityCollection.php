<?php

namespace Pie\JsonApi\Library;

use Cake\ORM\Query;
use IteratorAggregate;
use Traversable;
use WoohooLabs\Yin\JsonApi\Schema\Pagination\PageBasedPaginationProviderTrait;
use WoohooLabs\Yin\JsonApi\Schema\Pagination\PaginationLinkProviderInterface;

class EntityCollection implements IteratorAggregate, PaginationLinkProviderInterface
{
    use PageBasedPaginationProviderTrait;

    protected $query;
    protected $page;
    protected $size;

    public function __construct(Query $query, $page, $size)
    {
        $this->query = $query;
        $this->page = $page;
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getTotalItems()
    {
        return $this->query->count();
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *        <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return $this->query->all();
    }
}
