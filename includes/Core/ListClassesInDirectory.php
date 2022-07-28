<?php

/**
 * Class ListClassesInDirectory.
 *
 * @package    ApiOpenStudio\Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020 MIT license
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://github.com/WyriHaximus/php-list-classes-in-directory
 */

namespace ApiOpenStudio\Core;

use ArrayIterator;
use FilterIterator;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\Exception\IdentifierNotFound;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\AutoloadSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;

/**
 * Class ListClassesInDirectory
 *
 * List classes with directories or files.
 *
 * @see https://github.com/WyriHaximus/php-list-classes-in-directory
 */
class ListClassesInDirectory
{
    /**
     * get a list of all classes in the given directories.
     *
     * Based on: https://github.com/Roave/BetterReflection/blob/396a07c9d276cb9ffba581b24b2dadbb542d542e/demo/parsing-whole-directory/example2.php.
     *
     * @return iterable<string>
     */
    public function listClassesInDirectories(string ...$directories): iterable
    {
        $sourceLocator = new AggregateSourceLocator([
            new DirectoriesSourceLocator(
                $directories,
                (new BetterReflection())->astLocator()
            ),
            // ↓ required to autoload parent classes/interface from another directory than /src (e.g. /vendor)
            new AutoloadSourceLocator((new BetterReflection())->astLocator()),
        ]);

        foreach ($this->listClassesInSourceLocator($sourceLocator) as $class) {
            yield $class->getName();
        }
    }

    /**
     * get a list of all classes in the given directory.
     *
     * @return iterable<string>
     */
    public function listClassesInDirectory(string $directory): iterable
    {
        yield from $this->listClassesInDirectories($directory);
    }

    /**
     * get a list of all classes in the given file.
     *
     * @return iterable<string>
     */
    public function listClassesInFile(string $file): iterable
    {
        $sourceLocator = new AggregateSourceLocator([
            new SingleFileSourceLocator(
                $file,
                (new BetterReflection())->astLocator()
            ),
            // ↓ required to autoload parent classes/interface from another directory (e.g. /vendor)
            new AutoloadSourceLocator((new BetterReflection())->astLocator()),
        ]);

        foreach ($this->listClassesInSourceLocator($sourceLocator) as $class) {
            yield $class->getName();
        }
    }

    /**
     * get a list of all classes in the given files.
     *
     * @return iterable<string>
     */
    public function listClassesInFiles(string ...$files): iterable
    {
        foreach ($files as $file) {
            foreach ($this->listClassesInFile($file) as $class) {
                yield $class;
            }
        }
    }

    /**
     * @return iterable<string>
     */
    protected function listInstantiatableClassesInDirectories(string ...$directories): iterable
    {
        $iterator = $this->listClassesInDirectories(...$directories);

        return new class (new ArrayIterator([...$iterator])) extends FilterIterator {
            public function accept(): bool
            {
                $className = $this->getInnerIterator()->current();
                try {
                    $reflectionClass = ReflectionClass::createFromName($className);

                    return $reflectionClass->isInstantiable();
                } catch (IdentifierNotFound $exception) {
                    return false;
                }
            }
        };
    }

    /**
     * @return iterable<string>
     */
    public function listInstantiatableClassesInDirectory(string $directory): iterable
    {
        yield from $this->listInstantiatableClassesInDirectories($directory);
    }

    /**
     * @return iterable<string>
     */
    public function listNonInstantiatableClassesInDirectories(string ...$directories): iterable
    {
        $iterator = $this->listClassesInDirectories(...$directories);

        return new class (new ArrayIterator([...$iterator])) extends FilterIterator {
            public function accept(): bool
            {
                $className = $this->getInnerIterator()->current();
                try {
                    $reflectionClass = ReflectionClass::createFromName($className);

                    return $reflectionClass->isInstantiable() === false;
                } catch (IdentifierNotFound $exception) {
                    return true;
                }
            }
        };
    }

    /**
     * @return iterable<string>
     */
    public function listNonInstantiatableClassesInDirectory(string $directory): iterable
    {
        yield from $this->listNonInstantiatableClassesInDirectories($directory);
    }

    /**
     * @internal
     *
     * @return iterable<ReflectionClass>
     */
    protected function listClassesInSourceLocator(AggregateSourceLocator $sourceLocator): iterable
    {
        /**
         * @phpstan-ignore-next-line
         * @psalm-suppress UndefinedClass
         */
        yield from class_exists(\Roave\BetterReflection\Reflector\ClassReflector::class)
            ? (new \Roave\BetterReflection\Reflector\ClassReflector($sourceLocator))->getAllClasses()
            : (new \Roave\BetterReflection\Reflector\DefaultReflector($sourceLocator))->reflectAllClasses();
    }
}
