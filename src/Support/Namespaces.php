<?php

namespace Modular\Support;

class Namespaces
{
    /**
     * Gets the namespace of a class from a relative path
     *
     * @param  string $path
     * @return string
     */
    public static function namespaceFromPath(string $path)
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
    public static function namespaceCombine(string $baseNamespace, string $namespace)
    {
        return ltrim($baseNamespace.rtrim('\\'.$namespace, '\\'), '\\');
    }

    /**
     * Get a path from a namespace
     *
     * @param  string $namespace
     * @param  bool $stripClass
     * @return string
     */
    public static function namespaceToPath(string $namespace, bool $stripClass = false)
    {
        $namespace = trim($namespace, '\\');
        if ($stripClass) {
            return str_replace('\\', '/', $namespace);
        }
        return str_replace('\\', '/', preg_replace('/\\.*?/', '', $namespace));
    }
}
