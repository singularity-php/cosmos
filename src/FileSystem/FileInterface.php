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
     * returns the directory interface of the file.
     *
     * @return DirectoryInterface
     */
    public function directory(): DirectoryInterface;
}