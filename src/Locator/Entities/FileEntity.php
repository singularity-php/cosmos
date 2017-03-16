<?php
/**
 * This file is part of the SINGULARITY PHP Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Singularity\Locator\Entities;


use Singularity\FileSystem\FileInterface;
use Singularity\Locator\LocatorEntityInterface;

class FileEntity implements LocatorEntityInterface
{
    private $file;

    public function __construct(FileInterface $file)
    {
        $this->file = $file;
    }

    /**
     * returns the type of the locator entity.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'file';
    }

    /**
     * returns the locator entity handle.
     *
     * @return mixed
     */
    public function getHandle(): ? FileInterface
    {
        return $this->file;
    }

}