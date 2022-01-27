<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Services\Search\Types\FindInterface;

interface SearchFactoryInterface
{
    function get(): FindInterface;
}
