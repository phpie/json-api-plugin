<?php

namespace Pie\JsonApi\Library;

use WoohooLabs\Yin\JsonApi\Schema\Pagination\PaginationLinkProviderInterface;

class PaginationLinkProvider implements PaginationLinkProviderInterface
{
    use PageBasedPaginationProviderTrait;

    protected $total;
    protected $page;
    protected $size;

    public function __construct($total, $page, $size)
    {
        $this->total = $total;
        $this->page = $page;
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getTotalItems()
    {
        return $this->total;
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
}
