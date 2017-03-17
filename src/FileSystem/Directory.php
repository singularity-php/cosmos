<?php
/**
 * This file is part of the SINGULARITY PHP Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Singularity\FileSystem;


use Singularity\FileSystem\Exceptions\FileSystemException;
use Singularity\FileSystem\Traits\PathTrait;
use Singularity\FileSystem\Traits\QueryBuilderTrait;

/**
 * Class Directory
 * @package Singularity\FileSystem
 */
class Directory implements DirectoryInterface
{
    use QueryBuilderTrait;
    use PathTrait;

    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $baseDirectory;
    /**
     * @var string
     */
    private $openDirectory;

    /**
     * Directory constructor.
     * @param string $name
     * @param string|null $baseDirectory
     * @param string|null $openDirectory
     * @throws FileSystemException
     */
    public function __construct(string $name, string $baseDirectory = null, string $openDirectory = null)
    {
        $baseDirectory = $this->marshalSubPath($baseDirectory ?? dirname($_SERVER['SCRIPT_FILENAME']));
        $this->path = $this->marshalPath($baseDirectory, $name);
        $this->baseDirectory = $baseDirectory;
        $this->openDirectory = realpath($openDirectory);

        if ( null === $openDirectory && ! empty(ini_get('open_basedir')) && is_dir(ini_get('open_basedir')) ) {
            $this->openDirectory = realpath(ini_get('open_basedir'));
        }
        else if ( null === $openDirectory ) {
            $this->openDirectory = dirname($_SERVER['SCRIPT_FILENAME']);
        }
        else if ( false === $this->openDirectory ) {
            throw new FileSystemException('open base directory is not reachable');
        }
    }

    /**
     * executes the callback on the results of the provided query.
     *
     * @param string $query
     * @param callable $callback
     * @return void
     */
    public function each(string $query, callable $callback): void
    {
        $pattern = $this->buildPattern(ltrim($this->marshalSubPath($query), '/\\'));
        $result = glob($this->path.'/'.$pattern, GLOB_BRACE);

        foreach ( $result as $current ) {
            $entity = is_file($current)
                ? new File(
                    basename($current),
                    dirname($current) !== $this->path
                        ? new Directory(basename(dirname($current)), dirname(dirname($current)))
                        : $this
                )
                : new Directory(basename($current), dirname($current), $this->openDirectory)
            ;

            $callback($entity);
        }
    }

    /**
     * returns the file system entity for the provided query.
     *
     * @param string $query
     * @return FileSystemEntityInterface|DirectoryInterface|FileInterface|null
     */
    public function find(string $query): ? FileSystemEntityInterface
    {
        $pattern = $this->buildPattern(ltrim($this->marshalSubPath($query), '/\\'));
        $result = glob($this->path.'/'.$pattern, GLOB_BRACE);

        if ( empty($result) ) {
            return null;
        }

        $directory = dirname($result[0]);
        $name = basename($result[0]);

        if ( is_dir($result[0]) ) {
            return new Directory($name, $directory);
        }

        return new File(
            $name,
            $directory !== $this->path
                ? new Directory(basename($directory), dirname($directory), $this->openDirectory)
                : $this
        );
    }

    /**
     * checks if the provided query points to a directory.
     *
     * @param string $query
     * @return mixed
     */
    public function isDirectory(string $query): bool
    {
        $pattern = $this->buildPattern(ltrim($this->marshalSubPath($query), '/\\'));
        $result = glob($this->path.'/'.$pattern, GLOB_BRACE);

        if ( empty($result) ) {
            return false;
        }

        return is_dir($this->path.'/'.$result[0]);
    }

    /**
     * checks if the provided query points to a file.
     *
     * @param string $query
     * @return mixed
     */
    public function isFile(string $query): bool
    {
        $pattern = $this->buildPattern(ltrim($this->marshalSubPath($query), '/\\'));
        $result = glob($this->path.'/'.$pattern, GLOB_BRACE);

        if ( empty($result) ) {
            return false;
        }

        return is_file($this->path.'/'.$result[0]);
    }

    /**
     * enforces the presence of the query as a directory when a query is provided. When no query is provided
     * the parent directory will be returned.
     *
     * @param string|null $query
     * @throws FileSystemException when the current directory is not writable
     * @return DirectoryInterface
     */
    public function directory(string $query = null): DirectoryInterface
    {
        if ( null === $query && 0 !== strpos($this->baseDirectory, $this->openDirectory) ) {
            var_dump($this->baseDirectory, $this->openDirectory);
            throw new FileSystemException('Access denied, directory outside the open base directory');
        }

        if ( null === $query ) {
            return new Directory(basename($this->baseDirectory), dirname($this->baseDirectory), $this->openDirectory);
        }

        $path = ltrim($this->marshalSubPath($query), '/\\');

        if ( ! is_writeable($this->path) && ! is_dir($this->path.'/'.$path) ) {
            throw new FileSystemException(
                'Unable to create directory, target is not writable: '.ltrim($query, '/\\')
            );
        }

        if ( is_dir($this->path.'/'.$path) ) {
            return new Directory($path, $this->path, $this->openDirectory);
        }

        mkdir($this->path.'/'.$path);

        return new Directory($path, $this->path, $this->openDirectory);
    }

    /**
     * enforces the presence of the provided query as a file.
     *
     * @param string $query
     * @throws FileSystemException
     * @return FileInterface
     */
    public function file(string $query): FileInterface
    {
        $path = ltrim($this->marshalSubPath($query), '/\\');

        if ( file_exists($this->path.'/'.$path) && ! is_writable($this->path.'/'.$path) ) {
            throw new FileSystemException(
                'Unable to create directory, target is not writable: '.ltrim($query, '/\\')
            );
        }

        if ( ! file_exists($this->path.'/'.$path) && ! $this->isWritable() ) {
            throw new FileSystemException(
                'Directory is not writable: '.$this->path
            );
        }

        if ( is_file($this->path.'/'.$path) ) {
            return new File($path, $this);
        }

        if ( false !== strpos($path, '/') ) {
            $directoryName = dirname($path);
            $fileName = basename($path);
            return $this->directory($directoryName)->file($fileName);
        }

        touch($this->path.'/'.$path);

        return new File($path, $this);
    }

    /**
     * checks whether the file or directory is readable.
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        return is_readable($this->path);
    }

    /**
     * checks whether the file or directory is writable.
     *
     * @return bool
     */
    public function isWritable(): bool
    {
        return is_writable($this->path);
    }

    /**
     * deletes the file or directory.
     *
     * @throws FileSystemException
     * @return void
     */
    public function delete(): void
    {
        if ( ! $this->isWritable() ) {
            throw new FileSystemException(
                'File access denied: '.$this->path
            );
        }

        if ( ! $this->exists() ) {
            throw new FileSystemException(
                'Nothing to delete: '.$this->path
            );
        }

        $this->each('*', function(FileSystemEntityInterface $entity) {
            $entity->delete();
        });

        rmdir($this->path);
    }

    /**
     * checks whether the file or directory is a link.
     *
     * @return bool
     */
    public function isLink(): bool
    {
        return is_link($this->path);
    }

    /**
     * checks whether the file or directory does exist.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return file_exists($this->path);
    }

    /**
     * gets the path of this directory.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }


}