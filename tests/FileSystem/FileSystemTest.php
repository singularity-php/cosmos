<?php
/**
 * This file is part of the SINGULARITY PHP Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Singularity\Tests\FileSystem;


use PHPUnit\Framework\TestCase;
use Singularity\FileSystem\Directory;
use Singularity\FileSystem\DirectoryInterface;
use Singularity\FileSystem\FileSystem;


class FileSystemTest extends TestCase
{
    protected $testDirectory;

    public function setUp()
    {
        $this->testDirectory = __DIR__.'/../../mocks/FileSystem';
    }

    /**
     * @test
     */
    public function instanceTest()
    {
        $this->assertTrue(file_exists($this->testDirectory));

        $filesystem = new FileSystem($this->testDirectory);


        $this->assertSame(realpath($this->testDirectory), $filesystem->getPath());

        $current = $filesystem->find('/');

        $this->assertInstanceOf(Directory::class, $current);
        $this->assertInstanceOf(DirectoryInterface::class, $current);
        $this->assertSame(realpath($this->testDirectory), $current->getPath());
    }

    /**
     * @test
     * @expectedException \Singularity\FileSystem\Exceptions\FileSystemException
     */
    public function invalidInstanceTest()
    {
        $this->assertFalse(file_exists($this->testDirectory.'/invalid-path'));
        $filesystem = new FileSystem($this->testDirectory.'/invalid-path');
    }
}
