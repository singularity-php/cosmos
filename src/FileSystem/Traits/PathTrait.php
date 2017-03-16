<?php
/**
 * This file is part of the SINGULARITY PHP Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Singularity\FileSystem\Traits;


trait PathTrait
{
    /**
     * marshals (normalizes) a unix-like sub-path.
     *
     * @param string $path
     * @return string
     */
    protected function marshalSubPath(string $path): string
    {
        $wantedPath = str_replace('\\', '/', '/'.ltrim($path, '/\\'));

        $outbox = '/';

        foreach ( explode('/', $wantedPath) as $current ) {
            if ( empty($current) || '.' == $current ) {
                continue;
            }

            if ( '..' == $current ) {
                $outbox = dirname($outbox);
                continue;
            }

            $outbox .= ( $outbox !== '/' ? '/' : '' ).$current;
        }

        return $outbox;
    }

    /**
     * marshals a subPath by its origin path.
     *
     * @param string $originPath
     * @param string $subPath
     * @return string
     */
    protected function marshalPath(string $originPath, string $subPath): string
    {
        return $this->marshalSubPath($originPath.'/'.$subPath);
    }
}