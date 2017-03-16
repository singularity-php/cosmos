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


use Singularity\Locator\Exceptions\LocatorException;

/**
 * Class DescriptorCollection
 * @package Singularity\Locator
 */
class DescriptorCollection
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * descriptor getter.
     *
     * @param string $scheme
     * @return DescriptorInterface
     * @throws LocatorException
     */
    public function get(string $scheme): DescriptorInterface
    {
        if ( ! $this->has($scheme) ) {
            throw new LocatorException('Unknown scheme: '.$scheme);
        }

        return $this->items[strtolower($scheme)];
    }

    /**
     * checks whether a scheme is registered or not.
     *
     * @param string $scheme
     * @return DescriptorInterface
     */
    public function has(string $scheme): DescriptorInterface
    {
        return array_key_exists(strtolower($scheme), $this->items);
    }

    /**
     * sets a descriptor for a scheme.
     *
     * @param string $scheme
     * @param DescriptorInterface $descriptor
     */
    public function set(string $scheme, DescriptorInterface $descriptor)
    {
        $this->items[strtolower($scheme)] = $descriptor;
    }
}