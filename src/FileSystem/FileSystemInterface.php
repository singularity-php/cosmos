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


interface FileSystemInterface extends PathAwareInterface
{
    /**
     * returns the file system entity for the provided query.
     *
     * @param string $query
     * @return FileSystemEntityInterface|null
     */
    public function find(string $query): ? FileSystemEntityInterface;

    /**
     * enforces the presence of the provided query as a directory.
     *
     * @param string $query
     * @return DirectoryInterface
     */
    public function directory(string $query): DirectoryInterface;

    /**
     * enforces the presence of the provided query as a file.
     *
     * @param string $query
     * @return FileInterface
     */
    public function file(string $query): FileInterface;
}