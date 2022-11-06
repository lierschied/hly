<?php

namespace Core;

use DirectoryIterator;
use RuntimeException;

class ClassMapper
{

    /**
     * @var array<string, string>
     */
    private array $namespaces = [];

    private array $classMap = [];

    /**
     * @throws RuntimeException
     */
    public function __construct()
    {
    }

    /**
     * @throws RuntimeException
     */
    public function map(?array $namespaces = null): void
    {
        $this->namespaces = $namespaces ?? \Core\Shared\Config::get('app', 'classMapper');
        foreach ($this->namespaces as $namespace => $dir) {
            $path = __ROOT__ . DIRECTORY_SEPARATOR . $dir;
            if (!is_dir($path)) {
                throw new RuntimeException('Invalid path');
            }
            $this->load($path, $namespace);
        }
    }

    public function addNamespaces(array $namespaces): void
    {
        $this->namespaces = array_merge($this->namespaces, $namespaces);
    }

    public function classesWithinNamespace(string $namespace): array
    {
        $namespace = str_replace(['.', '/'], '\\', ucwords($namespace, './\\'));
        return array_filter($this->classMap, static fn($key) => str_starts_with($key, $namespace), ARRAY_FILTER_USE_KEY);
    }

    public function classesWithinMultipleNamespaces(array $namespaces): array
    {
        $classes = [];
        foreach ($namespaces as $namespace) {
            $classes = array_merge($this->classesWithinNamespace($namespace));
        }
        return $classes;
    }

    private function load(string $path, string $namespace): void
    {
        $path = rtrim($path, '/');
        $namespace = rtrim($namespace, '\\');
        $dir = new DirectoryIterator($path);

        foreach ($dir as $f) {
            if ($f->isDir() && !str_contains($f->getFilename(), '.')) {
                $_namespace = $namespace . '\\' . $f->getFilename();
                $this->load($f->getRealPath(), $_namespace);
            }
            if ($f->isFile() && str_ends_with($f->getFilename(), '.php')) {
                $_namespace = $namespace . '\\' . str_replace('.php', '', $f->getFilename());
                $this->classMap[$_namespace] = str_replace('\\', '/', $namespace) . '.php';
            }
        }
    }
}