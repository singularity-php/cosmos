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

class Directory implements DirectoryInterface
{
    use QueryBuilderTrait;
    use PathTrait;

    private $path;
    private $baseDirectory;

    public function __construct(string $name, string $baseDirectory = null)
    {
        $baseDirectory = $this->marshalSubPath($baseDirectory ?? dirname($_SERVER['SCRIPT_FILENAME']));
        $this->path = $this->marshalPath($baseDirectory, $name);
        $this->baseDirectory = $baseDirectory;
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
            $pathName = $this->path.'/'.$current;

            $entity = is_file($pathName)
                ? new File($pathName, $this->path)
                : new Directory($pathName, $this->path)
            ;

            $callback($entity);
        }
    }

    /**
     * returns the file system entity for the provided query.
     *
     * @param string $query
     * @return FileSystemEntityInterface|null
     */
    public function find(string $query): ? FileSystemEntityInterface
    {
        $pattern = $this->buildPattern(ltrim($this->marshalSubPath($query), '/\\'));
        $result = glob($this->path.'/'.$pattern, GLOB_BRACE);

        if ( empty($result) ) {
            return null;
        }

        $fullPath = $this->path.'/'.$result[0];
        $directory = dirname($fullPath);
        $name = basename($fullPath);

        if ( is_dir($fullPath) ) {
            return new Directory($name, $directory);
        }

        return new File($name, $directory);
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
     * enforces the presence of the provided query as a directory.
     *
     * @param string $query
     * @throws FileSystemException
     * @return DirectoryInterface
     */
    public function directory(string $query): DirectoryInterface
    {
        $path = ltrim($this->marshalSubPath($query), '/\\');

        if ( ! is_writeable($this->path.'/'.$path) ) {
            throw new FileSystemException(
                'Unable to create directory, target is not writable: '.ltrim($query, '/\\')
            );
        }

        if ( is_dir($this->path.'/'.$path) ) {
            return new Directory($path, $this->path);
        }

        mkdir($path);

        return new Directory($path, $this->path);
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

        if ( ! is_writeable($path) ) {
            throw new FileSystemException(
                'Unable to create directory, target is not writable: '.ltrim($query, '/\\')
            );
        }

        if ( is_file($this->path.'/'.$path) ) {
            return new File($path, $this->path);
        }

        if ( false !== strpos($path, '/') ) {
            $directoryName = dirname($path);
            $fileName = basename($path);
            return $this->directory($directoryName)->file($fileName);
        }

        touch($this->path.'/'.$path);

        return new File($path, $this->path);
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

        unlink($this->path);
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