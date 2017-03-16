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


use Singularity\FileSystem\DirectoryInterface;
use Singularity\Locator\Exceptions\LocatorException;

interface LocatorInterface
{
    /**
     * locates a resource by its application url.
     *
     * @param $applicationURL
     * @return null|LocatorEntityInterface
     */
    public function locate($applicationURL): ? LocatorEntityInterface;

    /**
     * registers a descriptor.
     *
     * @param DescriptorInterface[] ...$descriptors
     * @throws LocatorException when a descriptor scheme was already known
     * @return void
     */
    public function register(DescriptorInterface ... $descriptors): void;

    /**
     * query command for directory placeholder.
     *
     * Sets a directory for a placeholder when a directory instance is provided. Returns a known
     * Directory instance when a placeholder is known and no directory instance is provided. If the
     * queried placeholder is not known, null will be returned.
     *
     * @param string $placeholder
     * @param DirectoryInterface|null $directory
     * @return DirectoryInterface|null
     */
    public function directory(string $placeholder, DirectoryInterface $directory = null): ? DirectoryInterface;
}