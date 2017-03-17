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


interface FileSystemEntityInterface extends PathAwareInterface
{
    /**
     * checks whether the file or directory is readable.
     *
     * @return bool
     */
    public function isReadable(): bool;

    /**
     * checks whether the file or directory is writable.
     *
     * @return bool
     */
    public function isWritable(): bool;

    /**
     * deletes the file or directory.
     *
     * @return void
     */
    public function delete(): void;

    /**
     * checks whether the file or directory is a link.
     *
     * @return bool
     */
    public function isLink(): bool;

    /**
     * checks whether the file or directory does exist.
     *
     * @return bool
     */
    public function exists(): bool;
}