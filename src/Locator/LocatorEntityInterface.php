<?php
/**
 * This file is part of the SINGULARITY PHP Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Singularity\Locator;


interface LocatorEntityInterface
{
    /**
     * returns the type of the locator entity.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * returns the locator entity handle.
     *
     * @return mixed
     */
    public function getHandle();
}