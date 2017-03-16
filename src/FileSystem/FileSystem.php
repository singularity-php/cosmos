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
 * Class FileSystem
 * @package Singularity\FileSystem
 */
class FileSystem implements FileSystemInterface
{
    use QueryBuilderTrait;
    use PathTrait;

    /**
     * @var
     */
    protected $workingDirectory;

    /**
     * FileSystem constructor.
     * @param string|null $workingDirectory
     * @throws FileSystemException
     */
    public function __construct(string $workingDirectory = null)
    {
        $previsionedDirectory = $this->marshalSubPath($workingDirectory ?? dirname($_SERVER['SCRIPT_FILENAME']));

        $baseDirectory = dirname($previsionedDirectory);
        $name = basename($previsionedDirectory);

        if ( ! is_dir($previsionedDirectory) ) {
            throw new FileSystemException(
                'provided working directory is not a directory: '.$workingDirectory
            );
        }

        $this->workingDirectory = new Directory($name, $baseDirectory);
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
        $result = glob($this->workingDirectory->getPath().'/'.$pattern, GLOB_BRACE);

        if ( empty($result) ) {
            return null;
        }

        $directory = dirname($result[0]);
        $name = basename($result[0]);

        if ( is_dir($result[0]) ) {
            return new Directory($name, $directory);
        }

        return new File($name, $directory);
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
        $path = $this->workingDirectory->getPath().'/'.ltrim($this->marshalSubPath($query), '/\\');

        if ( ! is_writeable($path) ) {
            throw new FileSystemException(
                'Unable to create directory, target is not writable: '.$path
            );
        }

        $baseDirectory = dirname($path);
        $name = basename($path);

        if ( is_dir($path) ) {
            return new Directory($name, $baseDirectory);
        }

        mkdir($path);

        return new Directory($name, $baseDirectory);
    }

    /**
     * enforces the presence of the provided query as a file.
     *
     * @param string $query
     * @return FileInterface
     */
    public function file(string $query): FileInterface
    {
        if ( false !== strpos('/', $query) ) {
            $directory = dirname($query);
            $filename = basename($query);
            return $this->workingDirectory->directory($directory)->file($filename);
        }

        return $this->workingDirectory->file($query);
    }

    /**
     * returns the working directory.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->workingDirectory->getPath();
    }
}