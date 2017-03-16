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

/**
 * Class File
 * @package Singularity\FileSystem
 */
class File implements FileInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var mixed
     */
    private $extension;

    /**
     * @var string
     */
    private $basename;

    /**
     * File constructor.
     * @param string $pathName
     */
    public function __construct(string $pathName)
    {
        $this->path = dirname($pathName);
        $this->filename = basename($pathName);
        $this->extension = pathinfo($pathName, PATHINFO_EXTENSION);
        $this->basename = basename($pathName, $this->extension);
    }

    /**
     * returns the contents of the file json decoded.
     *
     * @param bool $forceArray
     * @param int $depth
     * @return array
     */
    public function getJSON(bool $forceArray = true, int $depth = 512): array
    {
        return json_decode($this->get(), $forceArray, $depth);
    }

    /**
     * stores the provided variant as json to the current file.
     *
     * @param $variant
     * @return mixed
     */
    public function putJSON($variant): void
    {
        $this->put(json_encode($variant, JSON_PRETTY_PRINT));
    }

    /**
     * stores the provided contents into the file.
     *
     * @param string $contents
     * @throws FileSystemException when the file is immutable
     * @return void
     */
    public function put(string $contents): void
    {
        if ( ! $this->isWritable() ) {
            throw new FileSystemException(
                'File is immutable: '.$this->filename
            );
        }

        file_put_contents($this->path.'/'.$this->filename, $contents);
    }

    /**
     * returns the contents of the file.
     *
     * @throws FileSystemException when the file access was denied
     * @return string
     */
    public function get(): string
    {
        if ( ! $this->isReadable() ) {
            throw new FileSystemException(
                'File access denied'
            );
        }

        return file_get_contents($this->path.'/'.$this->filename);
    }

    /**
     * pulls (includes) the file and returns optionally returned contents.
     *
     * @throws FileSystemException when teh file access was denied
     * @return mixed
     */
    public function pull()
    {
        if ( ! $this->isReadable() ) {
            throw new FileSystemException(
                'File inclusion denied'
            );
        }

        return include $this->path.'/'.$this->filename;
    }

    /**
     * checks whether the file or directory is readable.
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        return is_readable($this->path.'/'.$this->filename);
    }

    /**
     * checks whether the file or directory is writable.
     *
     * @return bool
     */
    public function isWritable(): bool
    {
        return is_writable($this->path.'/'.$this->filename);
    }

    /**
     * deletes the file or directory.
     *
     * @throws FileSystemException when the file access is denied
     * @return void
     */
    public function delete(): void
    {
        if ( ! $this->isWritable() ) {
            throw new FileSystemException(
                'File access denied: '.$this->path.'/'.$this->filename
            );
        }

        if ( ! $this->exists() ) {
            throw new FileSystemException(
                'Nothing to delete: '.$this->path.'/'.$this->filename
            );
        }

        unlink($this->path.'/'.$this->filename);
    }

    /**
     * checks whether the file or directory is a link.
     *
     * @return bool
     */
    public function isLink(): bool
    {
        return is_link($this->path.'/'.$this->filename);
    }

    /**
     * returns the directory interface of the file.
     *
     * @return DirectoryInterface
     */
    public function directory(): DirectoryInterface
    {
        return new Directory($this->path);
    }

    /**
     * checks whether the file or directory does exist.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return file_exists($this->path.'/'.$this->filename);
    }


}