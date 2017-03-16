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

/**
 * Class Locator
 * @package Singularity\Locator
 */
class Locator implements LocatorInterface
{
    /**
     * @var DescriptorCollection
     */
    private $descriptors;

    /**
     * @var array
     */
    private $placeholders = [];

    /**
     * Locator constructor.
     */
    public function __construct()
    {
        $this->descriptors = new DescriptorCollection();
    }

    /**
     * locates a resource by its application url.
     *
     * @param $applicationURL
     * @throws LocatorException
     * @return null|LocatorEntityInterface
     */
    public function locate($applicationURL): ? LocatorEntityInterface
    {
        if ( false === filter_var($applicationURL, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) ) {
            throw new LocatorException('Invalid locator url: '.$applicationURL);
        }

        $url = parse_url($applicationURL);

        if ( ! array_key_exists('scheme', $url) ) {
            throw new LocatorException('locator url has no scheme: '.$applicationURL);
        }

        $primary = $url['host'];
        $secondary = $url['path'];
        $options = [
            'query' => $url['query'] ?? [],
            'scheme' => $url['scheme'],
            'url' => $applicationURL,
        ];

        if ( ! $this->descriptors->has($url['scheme']) ) {
            throw new LocatorException('Unsupported scheme: '.$url['scheme']);
        }

        return $this->descriptors->get($url['scheme'])->make($this, $primary, $secondary, $options);
    }

    /**
     * registers a descriptor.
     *
     * @param DescriptorInterface[] ...$descriptors
     * @throws LocatorException when a descriptor scheme was already known
     * @return void
     */
    public function register(DescriptorInterface ... $descriptors): void
    {
        foreach ( $descriptors as $current ) {
            if ( $this->descriptors->has($current->getScheme()) ) {
                throw new LocatorException(
                    sprintf(
                        'scheme `%s` already known',
                        $current->getScheme()
                    )
                );
            }

            $this->descriptors->set($current->getScheme(), $current);
        }
    }

    /**
     * query command for directory placeholder.
     *
     * Sets a directory for a placeholder when a directory instance is provided. Returns a known
     * Directory instance when a placeholder is known and no directory instance is provided. If the
     * queried placeholder is not known, null will be returned.
     *
     * @param string $placeholder
     * @param DirectoryInterface|null $directory
     * @throws LocatorException in case of an empty placeholder
     * @return DirectoryInterface|null
     */
    public function directory(string $placeholder, DirectoryInterface $directory = null): ? DirectoryInterface
    {
        if ( empty($placeholder) ) {
            throw new LocatorException('Placeholder can not be an empty value');
        }

        if ( $directory instanceof DirectoryInterface ) {
            $this->placeholders[strtolower($placeholder)] = $placeholder;
        }

        if ( ! array_key_exists(strtolower($placeholder), $this->placeholders) ) {
            throw new LocatorException('Unknown placeholder: '.$placeholder);
        }

        $this->placeholders[strtolower($placeholder)] = $directory;
    }

}