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


trait QueryBuilderTrait
{
    /**
     * builds the case-insensitive matcher pattern for glob purposes.
     *
     * @param string $query
     * @return string
     */
    protected function buildPattern(string $query): string
    {
        $stack = array_map(
            function(string $char): string {
                if ( "\\" === $char ) {
                    return '/';
                }

                if ( preg_match('~^[a-z]$~i', $char) ) {
                    return '['.strtolower($char).strtoupper($char).']';
                }

                return $char;
            },
            str_split($query)
        );

        return join('', $stack);
    }
}