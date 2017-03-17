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

        $this->workingDirectory = new Directory($name, $baseDirectory, $previsionedDirectory);
    }

    /**
     * returns the file system entity for the provided query.
     *
     * @param string $query
     * @return FileSystemEntityInterface|DirectoryInterface|FileInterface|null
     */
    public function find(string $query): ? FileSystemEntityInterface
    {
       return $this->workingDirectory->find($query);
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
        return $this->workingDirectory->directory($query);
    }

    /**
     * enforces the presence of the provided query as a file.
     *
     * @param string $query
     * @return FileInterface
     */
    public function file(string $query): FileInterface
    {
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