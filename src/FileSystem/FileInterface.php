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


interface FileInterface extends FileSystemEntityInterface
{
    /**
     * returns the contents of the file json decoded.
     *
     * @return array
     */
    public function getJSON(): array;

    /**
     * stores the provided variant as json to the current file.
     *
     * @param $variant
     * @return void
     */
    public function putJSON($variant): void;

    /**
     * stores the provided contents into the file.
     *
     * @param string $contents
     * @return void
     */
    public function put(string $contents): void;

    /**
     * returns the contents of the file.
     *
     * @return string
     */
    public function get(): string;

    /**
     * pulls (includes) the file and returns optionally returned contents.
     *
     * @return mixed
     */
    public function pull();

    /**
     * copies the current file to the new location with an optional alternative file name.
     *
     * @param DirectoryInterface $directory
     * @param string|null $name
     */
    public function copy(DirectoryInterface $directory, string $name = null): void;

    /**
     * renames the current file to the provided new name.
     *
     * @param string $newName
     */
    public function rename(string $newName): void;

    /**
     * moves the current file to the new location with an optional alternative file name.
     *
     * @param DirectoryInterface $directory
     * @param string|null $name
     */
    public function move(DirectoryInterface $directory, string $name = null): void;

    /**
     * returns the directory interface of the file.
     *
     * @return DirectoryInterface
     */
    public function directory(): DirectoryInterface;

    /**
     * returns the filename of the current file.
     *
     * @return string
     */
    public function getFilename(): string;

    /**
     * returns the basename of the current file.
     *
     * @return string
     */
    public function getBasename(): string;

    /**
     * returns the extension of the current file.
     *
     * @return string
     */
    public function getExtension(): string;

    /**
     * returns the path to the current file.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * returns the full pathname to the file.
     *
     * @return string
     */
    public function getPathname(): string;
}