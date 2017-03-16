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


interface DirectoryInterface extends FileSystemEntityInterface
{
    /**
     * executes the callback on the results of the provided query.
     *
     * @param string $query
     * @param callable $callback
     * @return void
     */
    public function each(string $query, callable $callback): void;

    /**
     * returns the file system entity for the provided query.
     *
     * @param string $query
     * @return FileSystemEntityInterface|null
     */
    public function find(string $query): ? FileSystemEntityInterface;

    /**
     * checks if the provided query points to a directory.
     *
     * @param string $query
     * @return mixed
     */
    public function isDirectory(string $query): bool;

    /**
     * checks if the provided query points to a file.
     *
     * @param string $query
     * @return mixed
     */
    public function isFile(string $query): bool;

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