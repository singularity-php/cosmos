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
use Singularity\FileSystem\Traits\QueryBuilderTrait;

/**
 * Class FileSystem
 * @package Singularity\FileSystem
 */
class FileSystem implements FileSystemInterface
{
    use QueryBuilderTrait;

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
        $workingDirectory = $workingDirectory ?? dirname($_SERVER['SCRIPT_FILENAME']);

        if ( ! is_dir($workingDirectory) ) {
            throw new FileSystemException(
                'provided working directory is not a directory: '.$workingDirectory
            );
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
        $pattern = $this->buildPattern(ltrim($query, '/\\'));
        $result = glob($this->workingDirectory.'/'.$pattern, GLOB_BRACE);

        if ( empty($result) ) {
            return null;
        }

        if ( is_dir($result[0]) ) {
            return new Directory($this->workingDirectory.'/'.$result[0]);
        }

        return new File($this->workingDirectory.'/'.$result[0]);
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
        $path = $this->workingDirectory.'/'.ltrim($query, '/\\');

        if ( ! is_writeable($path) ) {
            throw new FileSystemException(
                'Unable to create directory, target is not writable: '.ltrim($query, '/\\')
            );
        }

        if ( is_dir($path) ) {
            return new Directory($path);
        }

        mkdir($path);

        return new Directory($path);
    }

    /**
     * enforces the presence of the provided query as a file.
     *
     * @param string $query
     * @return FileInterface
     */
    public function file(string $query): FileInterface
    {
        $directory = dirname($query);
        $filename = substr($query, strlen($directory) + 1);

        return $this->directory($directory)->file($filename);
    }

}