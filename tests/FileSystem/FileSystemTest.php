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
use Singularity\FileSystem\File;
use Singularity\FileSystem\FileInterface;
use Singularity\FileSystem\FileSystem;


class FileSystemTest extends TestCase
{
    /**
     * @var string
     */
    protected $testDirectory;

    /**
     * @var FileSystem
     */
    protected $fileSystem;

    public function setUp()
    {
        $this->testDirectory = __DIR__.'/../../mocks/FileSystem';
        $this->fileSystem = new FileSystem($this->testDirectory);
    }

    /**
     * @test
     * @covers \Singularity\FileSystem\FileSystem::__construct()
     * @uses \Singularity\FileSystem\FileSystem::getPath()
     * @uses \Singularity\FileSystem\FileSystemInterface::getPath()
     * @uses \Singularity\FileSystem\FileSystem::find()
     * @uses \Singularity\FileSystem\Directory::getPath()
     * @uses \Singularity\FileSystem\DirectoryInterface::getPath()
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
        $this->assertSame($filesystem->getPath(), $current->getPath());
    }

    /**
     * @test
     * @expectedException \Singularity\FileSystem\Exceptions\FileSystemException
     * @covers \Singularity\FileSystem\FileSystem::__construct()
     * @coversClass \Singularity\FileSystem\FileSystemException
     */
    public function invalidInstanceTest()
    {
        $this->assertFalse(file_exists($this->testDirectory.'/invalid-path'));
        $filesystem = new FileSystem($this->testDirectory.'/invalid-path');
    }

    /**
     * @test
     * @covers \Singularity\FileSystem\FileSystem::find()
     * @covers \Singularity\FileSystem\FileSystemInterface::find()
     * @uses \Singularity\FileSystem\Directory::exists()
     * @uses \Singularity\FileSystem\DirectoryInterface::exists()
     */
    public function findDirectoryTest()
    {
        $realPath = realpath($this->testDirectory.'/secondary-test-dir');
        $this->assertTrue(is_string($realPath), 'Testing Directory does not exists');

        $withoutSlashes = $this->fileSystem->find('seCondaRy-test-dir');
        $withPrecedingSlash = $this->fileSystem->find('/secondary-TEST-dir');
        $withPrecedingDotAndSlash = $this->fileSystem->find('./secondary-TeSt-dir');
        $withPrecedingDirUp = $this->fileSystem->find('../secondary-test-DiR');

        $this->assertInstanceOf(Directory::class, $withoutSlashes);
        $this->assertInstanceOf(DirectoryInterface::class, $withoutSlashes);
        $this->assertSame($realPath, $withoutSlashes->getPath());
        $this->assertTrue($withoutSlashes->exists());

        $this->assertInstanceOf(Directory::class, $withPrecedingSlash);
        $this->assertInstanceOf(DirectoryInterface::class, $withPrecedingSlash);
        $this->assertSame($realPath, $withPrecedingSlash->getPath());
        $this->assertTrue($withPrecedingSlash->exists());

        $this->assertInstanceOf(Directory::class, $withPrecedingDotAndSlash);
        $this->assertInstanceOf(DirectoryInterface::class, $withPrecedingDotAndSlash);
        $this->assertSame($realPath, $withPrecedingDotAndSlash->getPath());
        $this->assertTrue($withPrecedingDotAndSlash->exists());

        $this->assertInstanceOf(Directory::class, $withPrecedingDirUp);
        $this->assertInstanceOf(DirectoryInterface::class, $withPrecedingDirUp);
        $this->assertSame($realPath, $withPrecedingDirUp->getPath());
        $this->assertTrue($withPrecedingDirUp->exists());
    }

    /**
     * @test
     * @covers \Singularity\FileSystem\FileSystem::find()
     * @covers \Singularity\FileSystem\FileSystemInterface::find()
     * @coversClass \Singularity\FileSystem\Exceptions\FileSystemException
     */
    public function failedFindDirectoryTest()
    {
        $name = 'not-existing-directory';
        $target = realpath($this->testDirectory).'/'.$name;

        $this->assertFalse(is_dir($target));
        $this->assertNull($this->fileSystem->find($name));
    }

    /**
     * @test
     * @covers \Singularity\FileSystem\FileSystem::find()
     * @covers \Singularity\FileSystem\FileSystemInterface::find()
     * @uses \Singularity\FileSystem\Directory::getPath()
     * @uses \Singularity\FileSystem\DirectoryInterface::getPath()
     * @uses \Singularity\FileSystem\File::getPathname()
     * @uses \Singularity\FileSystem\File::getPath()
     * @uses \Singularity\FileSystem\FileInterface::getPathname()
     * @uses \Singularity\FileSystem\FileInterface::getPath()
     */
    public function findFileTest()
    {
        $name = 'test.file';
        $directory = 'test-dir';
        $target = realpath($this->testDirectory);

        $this->assertTrue(is_file($target.'/'.$directory.'/'.$name));

        $file = $this->fileSystem->find($directory.'/'.$name);

        $this->assertInstanceOf(File::class, $file);
        $this->assertInstanceOf(FileInterface::class, $file);
        $this->assertSame($target.'/'.$directory.'/'.$name, $file->getPathname());
        $this->assertSame($target.'/'.$directory, $file->getPath());

        $this->assertSame($target, $file->directory()->directory()->getPath());
    }

    /**
     * @test
     * @covers \Singularity\FileSystem\FileSystem::find()
     * @covers \Singularity\FileSystem\FileSystemInterface::find()
     */
    public function failedFindFileTest()
    {
        $name = 'not-existing-test.file';
        $directory = 'test-dir';
        $target = realpath($this->testDirectory);

        $this->assertFalse(is_file($target.'/'.$directory.'/'.$name));

        $file = $this->fileSystem->find($directory.'/'.$name);

        $this->assertNull($file);
    }

    /**
     * @test
     * @covers \Singularity\FileSystem\FileSystem::directory()
     * @covers \Singularity\FileSystem\FileSystemInterface::directory()
     * @uses \Singularity\FileSystem\Directory::exists()
     * @uses \Singularity\FileSystem\Directory::isWritable()
     * @uses \Singularity\FileSystem\Directory::getPath()
     * @uses \Singularity\FileSystem\DirectoryInterface::exists()
     * @uses \Singularity\FileSystem\DirectoryInterface::isWritable()
     * @uses \Singularity\FileSystem\DirectoryInterface::getPath()
     */
    public function createDirectoryTest()
    {
        $realPath = realpath($this->testDirectory.'/secondary-test-dir');
        $dirToCreate = 'must-be-empty';

        $this->assertFalse(is_dir($realPath.'/'.$dirToCreate));
        $this->assertFalse(file_exists($realPath.'/'.$dirToCreate));

        $forcePresenceDirectory = $this->fileSystem->directory('secondary-test-dir');
        $this->assertSame($forcePresenceDirectory->getPath(), $realPath);

        /** @var DirectoryInterface $workDir */
        $workDir = $this->fileSystem->find('secondary-test-dir');

        $this->assertSame($forcePresenceDirectory->getPath(), $workDir->getPath());

        $this->assertInstanceOf(Directory::class, $workDir);
        $this->assertInstanceOf(DirectoryInterface::class, $workDir);

        $this->assertTrue($workDir->isWritable(), 'test-environment is not writable');

        $newDir = $workDir->directory($dirToCreate);

        $this->assertInstanceOf(Directory::class, $newDir);
        $this->assertInstanceOf(DirectoryInterface::class, $newDir);

        $this->assertTrue($newDir->exists());
        $this->assertTrue(is_dir($realPath.'/'.$dirToCreate));
        $this->assertTrue(file_exists($realPath.'/'.$dirToCreate));

        $this->assertTrue(rmdir($realPath.'/'.$dirToCreate), 'creation test directory unlink failed');
        $this->assertFalse(is_dir($realPath.'/'.$dirToCreate), 'creation test directory not unlinked');
    }

    /**
     * @test
     * @covers \Singularity\FileSystem\FileSystem::file()
     * @covers \Singularity\FileSystem\FileSystem::getPath()
     * @covers \Singularity\FileSystem\FileSystemInterface::file()
     * @covers \Singularity\FileSystem\FileSystemInterface::getPath()
     * @uses \Singularity\FileSystem\Directory::isWritable()
     * @uses \Singularity\FileSystem\DirectoryInterface::isWritable()
     * @uses \Singularity\FileSystem\File::exists()
     * @uses \Singularity\FileSystem\File::getPathname()
     */
    public function createFileTest()
    {
        $directory = $this->fileSystem->find('.');
        $path = $this->fileSystem->getPath().'/secondary-test-dir';
        $name = 'creation.test';
        $pathname = 'secondary-test-dir/'.$name;

        $this->assertFalse(file_exists($path.'/'.$name));
        $this->assertTrue($directory->isWritable(), 'test-environment is not writable');

        $file = $this->fileSystem->file($pathname);

        $this->assertInstanceOf(File::class, $file);
        $this->assertInstanceOf(FileInterface::class, $file);
        $this->assertTrue(file_exists($path.'/'.$name));
        $this->assertTrue($file->exists());
        $this->assertSame($file->getPathname(), $path.'/'.$name);

        $this->assertTrue(unlink($file->getPathname()), 'unable to delete file by pathname');
        $this->assertFalse($file->exists());
    }

}
