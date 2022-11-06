<?php

namespace Core\Shared;

use Core\Exceptions\ContainerException;

/**
 * @method static void map(?array $namespaces = null)
 * @method static void addNamespaces(array $namespaces)
 * @method static array classesWithinNamespace(string $namespace)
 * @method static array classesWithinMultipleNamespaces(array $namespaces)
 *
 * @see \Core\ClassMapper
 */
class ClassMapper extends Shared
{
    public static function getIdentifier(): string
    {
        return 'classMapper';
    }

    /**
     * @throws ContainerException
     */
    public static function get_instance(): \Core\ClassMapper
    {
        return self::$containerInstance->get(self::getIdentifier());
    }
}