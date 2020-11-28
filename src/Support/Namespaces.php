<?php

namespace Modular\Support;

class Namespaces
{
    /**
     * Gets the namespace of a class from a file path
     *
     * @param  string $path
     * @return string
     */
    public static function fromPath(string $path)
    {
        $parts = explode('/', $path);

        // Check if contains filename
        if (pathinfo($path, PATHINFO_EXTENSION)) {
            array_pop($parts);
        }

        return trim(implode('\\', $parts), '\\');
    }

    /**
     * Combine a base and relative namespace
     *
     * @param  string $baseNamespace
     * @param  string $namespace
     * @return string
     */
    public static function combine(string $baseNamespace, string $namespace)
    {
        $baseNamespace = rtrim($baseNamespace, '\\');
        $namespace = trim($namespace, '\\');
        return $namespace ? $baseNamespace.'\\'.$namespace : $baseNamespace;
    }

    /**
     * Get a path from a namespace
     *
     * @param  string $namespace
     * @param  bool $stripClass
     * @return string
     */
    public static function toPath(string $namespace, bool $stripClass = false)
    {
        $namespace = trim($namespace, '\\');
        $path = str_replace('\\', '/', preg_replace('/\\.*?/', '', $namespace));

        if ($stripClass) {
            $parts = explode('/', $path);
            array_pop($parts);
            $path = implode('/', $parts);
        }

        return $path;
    }

    /**
     * Get the class name from a fully namespaced class
     *
     * @param string $namespace
     * @return mixed|string
     */
    public static function className(string $namespace)
    {
        $parts = explode('\\', $namespace);
        return array_pop($parts);
    }
}
