<?php
declare(strict_types=1);

namespace Common\Integrational\Test;

use Carbon\Carbon;
use Common\BaseClasses\BaseTestCase;
use Common\Entity\DirectoryData;
use Common\Entity\FileData;
use Common\Entity\FileSizeData;
use Common\File;

class FileTest extends BaseTestCase
{
    private const EXAMPLE_TEXT = 'This is example text.';
    private const EXAMPLE_DIRECTORY = '/var/cache/test_dir/inside/last_dir';
    private const EXAMPLE_FILE = '/var/cache/test_dir/inside/last_dir/test_file.txt';

    public function setUp(): void
    {
        if (file_exists(self::EXAMPLE_FILE)) {
            unlink(self::EXAMPLE_FILE);
        }

        if (file_exists(self::EXAMPLE_DIRECTORY)) {
            rmdir(self::EXAMPLE_DIRECTORY);
        }
    }

    public function testWillCreateFileInNonExistingDirectoryReadItAndDeleteFile(): void
    {
        self::assertFileDoesNotExist(self::EXAMPLE_FILE);
        self::assertFileDoesNotExist(self::EXAMPLE_DIRECTORY);

        File::write(self::EXAMPLE_FILE, self::EXAMPLE_TEXT);

        self::assertFileExists(self::EXAMPLE_FILE);
        self::assertFileExists(self::EXAMPLE_DIRECTORY);

        self::assertEquals(self::EXAMPLE_TEXT, File::read(self::EXAMPLE_FILE));

        File::delete(self::EXAMPLE_FILE);
        File::delete(self::EXAMPLE_DIRECTORY);

        self::assertFileDoesNotExist(self::EXAMPLE_FILE);
        self::assertFileDoesNotExist(self::EXAMPLE_DIRECTORY);
    }

    public function testWillCreateFileAndGetItsInfo(): void
    {
        $timeNow = Carbon::now();

        File::write(self::EXAMPLE_FILE, self::EXAMPLE_TEXT);

        $fileInfo = File::getInfo(self::EXAMPLE_FILE)
            ->setLastModified($timeNow);

        self::assertEquals(
            (new FileData())
                ->setName('test_file')
                ->setExt('txt')
                ->setFullName('test_file.txt')
                ->setType('text/plain')
                ->setHash(md5(self::EXAMPLE_TEXT))
                ->setLastModified($timeNow)
                ->setDirectory(
                    (new DirectoryData())
                        ->setName('last_dir')
                        ->setPath(self::EXAMPLE_DIRECTORY)
                        ->setMap(
                            array_slice(explode('/', self::EXAMPLE_DIRECTORY), 1)
                        )
                )
                ->setFileSize(
                    (new FileSizeData())
                        ->setBytes(21)
                        ->setKilobytes('0.02')
                        ->setMegabytes('0.00')
                        ->setGigabytes('0.00')
                ),
            $fileInfo
        );

        File::delete(self::EXAMPLE_FILE);
        File::delete(self::EXAMPLE_DIRECTORY);
    }
}
