<?php

namespace Core\Shared;

use Core\Exceptions\ContainerException;

/**
 * @method static mixed get(string|int $id, string $from, string $class, string $idColumn = 'id', ?string ...$select)
 * @method static array findBy(string $column, string $where, string $from)
 *
 * @see \Core\Database\Orm
 */
class Orm extends Shared
{
    public static function getIdentifier(): string
    {
        return 'orm';
    }

    /**
     * @throws ContainerException
     */
    public static function getInstance(): \Core\Http\Request
    {
        return self::$containerInstance->get(self::getIdentifier());
    }
}