<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Services\Search\Types\FullText;
use App\Services\Search\Types\Like;
use App\Services\Search\Types\RegExp;
use App\Services\Search\Types\FindInterface;
use Exception;

class SearchFactoryFactory implements SearchFactoryInterface
{
    public const REGEXP = 'regexp';

    public const LIKE = 'like';

    public const FULLTEXT = 'fulltext';

//    public const ELASTIC = 'elastic';

    public const TYPES_SEARCH = [
        self::REGEXP => RegExp::class,
        self::LIKE => Like::class,
        self::FULLTEXT => FullText::class,
//        self::ELASTIC => Elastic::class,
    ];

    /**
     * @throws Exception
     */
    public function __construct(private string $type)
    {
        if (array_key_exists($type, static::TYPES_SEARCH) === false) {
            throw new Exception('This type of error does not exist');
        }
    }

    public function get(): FindInterface
    {
        $entity = static::TYPES_SEARCH[$this->type];

        return new $entity();
    }
}
