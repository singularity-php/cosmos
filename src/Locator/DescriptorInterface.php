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


interface DescriptorInterface
{
    /**
     * returns the case insensitive scheme.
     *
     * @return string
     */
    public function getScheme(): string;

    /**
     * returns the entity class name.
     *
     * @return string
     */
    public function getEntityClassName(): string;

    /**
     * creates the locator entity.
     *
     * @param LocatorInterface $locator
     * @param string $primary
     * @param string $secondary
     * @param array $options
     * @return mixed
     */
    public function make(LocatorInterface $locator, string $primary, string $secondary, array $options): LocatorEntityInterface;
}