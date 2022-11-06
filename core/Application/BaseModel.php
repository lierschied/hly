<?php

namespace Core\Application;

use Core\Database\Table;
use Core\Shared\Orm;

/**
 * @property string $table override table name
 * @property string $idColumn override id column name
 */
class BaseModel
{
    private static array $allowed = [];
    protected static string $tableName;
    protected static array $hidden = [];

    public function __construct()
    {
    }

    /**
     * Returns a model based on given id
     * @param int $id
     * @return ?static
     */
    public static function get(int $id): ?static
    {
        $model = Orm::get($id, static::getTableName(), static::class, static::getIdColumn());

        return self::hideFields($model);
    }

    /**
     * Hides hidden fields to prevent exposing of sensitive data e.g. passwords
     *
     * @param BaseModel $model
     * @return BaseModel
     */
    private static function hideFields(BaseModel $model): BaseModel
    {
        foreach (static::$hidden as $hide) {
            unset($model->$hide);
        }
        return $model;
    }

    /**
     * Get a model by column (needs to be unique, eg. username, email etc.)
     *
     * @param string $column
     * @param string $where
     * @return static|null
     */
    public static function findBy(string $column, string $where): ?static
    {
        $data = Orm::findBy($column, $where, static::getTableName());
        if (empty($data)) {
            return null;
        }
        $model = new static();
        $model->setProperties($data);
        return $model;
    }

    /**
     * Returns the defined database table name of the Model
     * default is the Model lowercase classname
     * @return string
     */
    public static function getTableName(): string
    {
        return static::$tableName ?? strtolower(static::getName());
    }

    /**
     * @return string id column name
     */
    public static function getIdColumn(): string
    {
        return static::$idColumn ?? 'id';
    }

    /**
     * Returns the default table name
     * @return string
     */
    private static function getName(): string
    {
        return basename(str_replace('\\', '/', static::class));
    }

    /**
     * @param string $column
     * @return void
     */
    public static function allow(string $column): void
    {
        static::$allowed[] = $column;
    }

    /**
     * For adding the fetched assoc array as attributes, while excluding protected fields
     * @param array $data needs to be associative
     * @return void
     */
    private function setProperties(array $data): void
    {
        $hidden = array_filter(static::$hidden, static fn($v) => !in_array($v, static::$allowed, true));
        static::$allowed = [];
        foreach ($data as $property => $value) {
            if (in_array($property, $hidden, true)) {
                continue;
            }
            $this->$property = $value;
        }
    }

    public function save(): void
    {
        $values = [];
        foreach ([] as $col) {
            $values[$col] = $this->$col;
        }
        var_dump($values);
    }
}