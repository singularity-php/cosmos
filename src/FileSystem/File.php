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

/**
 * Class File
 * @package Singularity\FileSystem
 */
class File implements FileInterface
{
    use PathTrait;

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
     * @var DirectoryInterface
     */
    private $directory;

    /**
     * File constructor.
     * @param string $name
     * @param DirectoryInterface $directory
     * @throws FileSystemException
     */
    public function __construct(string $name, DirectoryInterface $directory)
    {
        $this->filename = $name;
        $this->extension = pathinfo($name, PATHINFO_EXTENSION);
        $this->basename = basename($name, '.'.$this->extension);
        $this->directory = $directory;
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
        if ( $this->exists() && ! $this->isWritable() ) {
            throw new FileSystemException(
                'File is immutable: '.$this->filename
            );
        }

        if ( ! $this->exists() && ! $this->directory->isWritable() ) {
            throw new FileSystemException(
                'Directory is immutable: '.$this->directory->getPath()
            );
        }

        file_put_contents($this->directory->getPath().'/'.$this->filename, $contents);
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

        return file_get_contents($this->directory->getPath().'/'.$this->filename);
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

        return include $this->directory->getPath().'/'.$this->filename;
    }

    /**
     * checks whether the file or directory is readable.
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        return is_readable($this->directory->getPath().'/'.$this->filename);
    }

    /**
     * checks whether the file or directory is writable.
     *
     * @return bool
     */
    public function isWritable(): bool
    {
        return is_writable($this->directory->getPath().'/'.$this->filename);
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
                'File access denied: '.$this->directory->getPath().'/'.$this->filename
            );
        }

        if ( ! $this->exists() ) {
            throw new FileSystemException(
                'Nothing to delete: '.$this->directory->getPath().'/'.$this->filename
            );
        }

        unlink($this->directory->getPath().'/'.$this->filename);
    }

    /**
     * checks whether the file or directory is a link.
     *
     * @return bool
     */
    public function isLink(): bool
    {
        return is_link($this->directory->getPath().'/'.$this->filename);
    }

    /**
     * returns the directory interface of the file.
     *
     * @return DirectoryInterface
     */
    public function directory(): DirectoryInterface
    {
        return $this->directory;
    }

    /**
     * checks whether the file or directory does exist.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return file_exists($this->directory->getPath().'/'.$this->filename);
    }

    /**
     * copies the current file to the new location with an optional alternative file name.
     *
     * @param DirectoryInterface $directory
     * @throws FileSystemException when the file or directory does not exists
     * @param string|null $name
     * @return FileInterface
     */
    public function copy(DirectoryInterface $directory, string $name = null): FileInterface
    {
        if ( ! $this->exists() ) {
            throw new FileSystemException('can not copy not existing files: '.$this->filename);
        }

        if ( ! $directory->exists() ) {
            throw new FileSystemException('can not copy to a non existing directory: '.$directory->getPath());
        }

        if ( ! $directory->isWritable() ) {
            throw new FileSystemException('can not copy to an immutable destination: '.$directory->getPath());
        }

        if ( false !== strpos(str_replace('\\', '/', $name), '/') ) {
            throw new FileSystemException('name parameter can not contain slashes');
        }

        $done = copy($this->directory->getPath().'/'.$this->filename, $directory->getPath().'/'.($name ?? $this->filename));

        if ( ! $done ) {
            throw new FileSystemException('copying failed, probably due to access issues');
        }

        return new File($name ?? $this->filename, $directory);
    }

    /**
     * renames the current file to the provided new name.
     *
     * @param string $newName
     * @throws FileSystemException
     */
    public function rename(string $newName): void
    {
        if ( ! $this->exists() ) {
            $this->filename = $newName;
            return;
        }

        if ( false !== strpos(str_replace('\\', '/', $newName), '/') ) {
            throw new FileSystemException('newName parameter can not contain slashes');
        }

        $done = rename($this->directory->getPath().'/'.$this->filename, $this->directory->getPath().'/'.$newName);

        if ( ! $done ) {
            throw new FileSystemException('renaming failed, probably due to access issues');
        }

        $this->filename = $newName;
    }

    /**
     * moves the current file to the new location with an optional alternative file name.
     *
     * @param DirectoryInterface $directory
     * @param string|null $name
     * @throws FileSystemException
     */
    public function move(DirectoryInterface $directory, string $name = null): void
    {
        if ( ! $this->exists() ) {
            throw new FileSystemException('can not move not existing file: '.$this->filename);
        }

        if ( ! $directory->exists() ) {
            throw new FileSystemException('can not move to not existing target directories: '.$directory->getPath());
        }

        if ( false !== strpos(str_replace('\\', '/', $name), '/') ) {
            throw new FileSystemException('name parameter can not contain slashes');
        }

        $name = $name ?? $this->filename;

        $done = rename(
            $this->getPathname(),
            $directory->getPath().'/'.$name
        );

        if ( ! $done ) {
            throw new FileSystemException('moving failed, probably due to access issues');
        }

        $this->directory = $directory;
        $this->filename = $name;
        $this->extension = pathinfo($name, PATHINFO_EXTENSION);
        $this->basename = basename($name, '.'.$this->extension);
    }

    /**
     * returns the filename of the current file.
     *
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * returns the basename of the current file.
     *
     * @return string
     */
    public function getBasename(): string
    {
        return $this->basename;
    }

    /**
     * returns the extension of the current file.
     *
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * returns the path to the current file.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->directory->getPath();
    }

    /**
     * returns the full pathname to the file.
     *
     * @return string
     */
    public function getPathname(): string
    {
        return $this->directory->getPath().'/'.$this->filename;
    }

}