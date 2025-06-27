<?php

declare(strict_types=1);

namespace App\Datatable;

class DatatableRequest
{

    public function __construct(public int $limit, public int $offset, public string $search, public array $sort, public int $draw)
    {
    }
}