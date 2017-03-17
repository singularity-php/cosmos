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


interface PathAwareInterface
{
    /**
     * returns the path to the current file or directory.
     *
     * @return string
     */
    public function getPath(): string;
}