<?php
/**
 * This file is part of the SINGULARITY PHP Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Singularity\Locator\Descriptors;


use Singularity\FileSystem\FileInterface;
use Singularity\FileSystem\FileSystem;
use Singularity\FileSystem\FileSystemInterface;
use Singularity\Locator\DescriptorInterface;
use Singularity\Locator\Entities\FileEntity;
use Singularity\Locator\Exceptions\NotFoundException;
use Singularity\Locator\LocatorEntityInterface;
use Singularity\Locator\LocatorInterface;

/**
 * Class FileDescriptor
 * @package Singularity\Locator\Descriptors
 */
class FileDescriptor implements DescriptorInterface
{
    /**
     * @var FileSystem|FileSystemInterface
     */
    private $fileSystem;

    /**
     * FileDescriptor constructor.
     * @param FileSystemInterface|null $fileSystem
     */
    public function __construct(FileSystemInterface $fileSystem = null)
    {
        $this->fileSystem = $fileSystem ?? new FileSystem();
    }

    /**
     * returns the case insensitive scheme.
     *
     * @return string
     */
    public function getScheme(): string
    {
        return 'file';
    }

    /**
     * returns the entity class name.
     *
     * @return string
     */
    public function getEntityClassName(): string
    {
        return FileEntity::class;
    }

    /**
     * creates the locator entity.
     *
     * @param LocatorInterface $locator
     * @param string $primary
     * @param string $secondary
     * @param array $options
     * @throws NotFoundException when the resource was not found
     * @return mixed
     */
    public function make(LocatorInterface $locator, string $primary, string $secondary, array $options): LocatorEntityInterface
    {
        $url = sprintf(
            '%s://%s/%s',
            $this->getScheme(),
            $primary,
            $secondary
        );

        $primary = trim($primary, '/\\');
        $secondary = trim($primary, '/\\');

        if ( ! empty($primary) && $locatedPrimary = $locator->directory($primary) ) {
            $file = $locatedPrimary->find($secondary);

            if ( ! $file instanceof FileInterface ) {
                throw new NotFoundException(
                    'Resource not a file: '.$url
                );
            }

            return new FileEntity($file);
        }
        else if ( empty($primary) && $locatedEntity = $this->fileSystem->file($primary.'/'. $secondary) ) {
            if ( ! $locatedEntity instanceof FileInterface ) {
                throw new NotFoundException(
                    'Resource not a file: '.$url
                );
            }

            return new FileEntity($locatedEntity);
        }
        else {
            throw new NotFoundException(
                'Resource not found: '.$url
            );
        }
    }

}