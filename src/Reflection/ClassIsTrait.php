<?php

/**
 * Copyright 2015-2019 info@neomerx.com
 * Modification Copyright 2021-2022 info@whoaphp.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace Whoa\Common\Reflection;

use Exception;
use FilesystemIterator;
use GlobIterator;
use ReflectionClass;
use ReflectionException;

use function assert;
use function array_key_exists;
use function class_exists;
use function get_declared_classes;
use function interface_exists;
use function is_file;
use function is_subclass_of;
use function ob_get_length;
use function ob_get_level;
use function realpath;

/**
 * @package Whoa\Common
 */
trait ClassIsTrait
{
    /**
     * @param string $class
     * @param string $interface
     * @return bool
     */
    protected static function classImplements(string $class, string $interface): bool
    {
        assert(class_exists($class));
        assert(interface_exists($interface));

        return array_key_exists($interface, class_implements($class));
    }

    /**
     * @param string $class
     * @param string $parentClass
     * @return bool
     */
    protected static function classExtends(string $class, string $parentClass): bool
    {
        assert(class_exists($class));
        assert(class_exists($parentClass));

        return array_key_exists($parentClass, class_parents($class));
    }

    /**
     * @param string $class
     * @param string $classOrInterface
     * @return bool
     */
    protected static function classInherits(string $class, string $classOrInterface): bool
    {
        assert(class_exists($class));
        assert(class_exists($classOrInterface) || interface_exists($classOrInterface));

        return is_subclass_of($class, $classOrInterface);
    }

    /**
     * @param string[] $classes
     * @param string $interface
     * @return iterable
     */
    protected static function selectClassImplements(array $classes, string $interface): iterable
    {
        foreach ($classes as $className) {
            if (static::classImplements($className, $interface) === true) {
                yield $className;
            }
        }
    }

    /**
     * @param string[] $classes
     * @param string $parentClass
     * @return iterable
     */
    protected static function selectClassExtends(array $classes, string $parentClass): iterable
    {
        foreach ($classes as $className) {
            if (static::classExtends($className, $parentClass) === true) {
                yield $className;
            }
        }
    }

    /**
     * @param string[] $classes
     * @param string $classOrInterface
     * @return iterable
     */
    protected static function selectClassInherits(array $classes, string $classOrInterface): iterable
    {
        foreach ($classes as $className) {
            if (static::classInherits($className, $classOrInterface) === true) {
                yield $className;
            }
        }
    }

    /**
     * Reads file(s) by specified path mask and select only those which implement given class or interface.
     * @param string $path
     * @param string $classOrInterface
     * @return iterable
     * @throws ReflectionException
     */
    protected static function selectClasses(string $path, string $classOrInterface): iterable
    {
        $selectedFiles = [];

        $flags = FilesystemIterator::SKIP_DOTS | FilesystemIterator::CURRENT_AS_PATHNAME;
        foreach (new GlobIterator($path, $flags) as $filePath) {
            if (is_file($filePath) === true) {
                $filePath = realpath($filePath);

                $obLevel = ob_get_level();
                $obLength = ob_get_length();

                try {
                    require_once $filePath;
                } catch (Exception $ex) {
                    // Files might have syntax errors etc.
                    // For the purposes of this method it doesn't matter so just skip it.
                    continue;
                }

                assert(
                    ob_get_level() === $obLevel && ob_get_length() === $obLength,
                    "File `$filePath` sends data to output buffer. " .
                    "This function should not be used with such files. " .
                    "Please correct input path `$path`."
                );

                $selectedFiles[$filePath] = true;
            }
        }

        foreach (get_declared_classes() as $class) {
            // if class actually implements requested one and ...
            if ($class === $classOrInterface || static::classInherits($class, $classOrInterface) === true) {
                // ... it was loaded from a file we've selected then...
                $reflectionClass = new ReflectionClass($class);
                if ($reflectionClass->isInstantiable() === true &&
                    ($classFileName = $reflectionClass->getFileName()) !== false &&
                    array_key_exists($classFileName, $selectedFiles) === true
                ) {
                    // ... that's what we need.
                    yield $class;
                }
            }
        }
    }
}
