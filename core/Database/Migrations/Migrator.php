<?php

namespace Core\Database\Migrations;

use Core\ClassMapper;
use Core\Database\Attributes\Column;
use Throwable;

class Migrator
{

    private array $models;
    private string $sql;
    private array $columns = [];

    public function __construct()
    {
        $this->loadModels();
    }

    public function loadModels(): void
    {
        $classMapper = new ClassMapper();
        $classMapper->map(['App\\Models' => 'app/Models']);
        $this->models = array_keys($classMapper->classesWithinNamespace('App\\Models'));
    }

    public function generateSql(): string
    {
        $dbs = new DBSchema();
        foreach ($this->models as $model) {
            try {
                $model = new ModelParser($model);
                $sql = sprintf(/** @lang text */ "CREATE TABLE `%s` (", $model->getName());
                $r = $model->columnsFromReflectionProperty();
                foreach ($r as $k => $v) {
                    $sql .= sprintf(",\n\t`%s` %s", $k ,$v);
                }
                $sql .= PHP_EOL . ");";
                var_dump($sql);
            } catch (Throwable $e) {
                echo $e->getMessage();
            }
        }
        return "";
    }


}