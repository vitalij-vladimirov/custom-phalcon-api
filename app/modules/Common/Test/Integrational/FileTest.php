<?php
declare(strict_types=1);

namespace Common\Integrational\Test;

use Carbon\Carbon;
use Common\BaseClass\BaseTestCase;
use Common\Entity\DirectoryData;
use Common\Entity\FileData;
use Common\Entity\FileSizeData;
use Common\File;

class FileTest extends BaseTestCase
{
    private const EXAMPLE_TEXT = 'This is example text.';
    private const EXAMPLE_DIRECTORY = '/var/cache/test_dir/inside/last_dir';
    private const EXAMPLE_FILE = '/var/cache/test_dir/inside/last_dir/test_file.txt';
    private const EXAMPLE_FILE_COPY = '/var/cache/test_dir/inside/last_dir/test_file_copy.txt';

    public function setUp(): void
    {
        if (file_exists(self::EXAMPLE_FILE)) {
            unlink(self::EXAMPLE_FILE);
        }

        if (file_exists(self::EXAMPLE_FILE_COPY)) {
            unlink(self::EXAMPLE_FILE_COPY);
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

    public function testWillCopyFile(): void
    {
        File::write(self::EXAMPLE_FILE, self::EXAMPLE_TEXT);

        self::assertFileExists(self::EXAMPLE_FILE);
        self::assertFileDoesNotExist(self::EXAMPLE_FILE_COPY);

        File::copy(self::EXAMPLE_FILE, self::EXAMPLE_FILE_COPY);

        self::assertFileExists(self::EXAMPLE_FILE);
        self::assertFileExists(self::EXAMPLE_FILE_COPY);
    }

    public function testWillMoveFile(): void
    {
        File::write(self::EXAMPLE_FILE, self::EXAMPLE_TEXT);

        self::assertFileExists(self::EXAMPLE_FILE);
        self::assertFileDoesNotExist(self::EXAMPLE_FILE_COPY);

        File::move(self::EXAMPLE_FILE, self::EXAMPLE_FILE_COPY);

        self::assertFileDoesNotExist(self::EXAMPLE_FILE);
        self::assertFileExists(self::EXAMPLE_FILE_COPY);
    }

    public function testReadDirectoryContent(): void
    {
        $hiddenFile = self::EXAMPLE_DIRECTORY . '/.hidden_file.txt';
        $subDirFile = self::EXAMPLE_DIRECTORY . '/sub_directory/test.txt';

        File::write(self::EXAMPLE_FILE, self::EXAMPLE_TEXT);
        File::write(self::EXAMPLE_FILE_COPY, self::EXAMPLE_TEXT);

        File::copy(self::EXAMPLE_FILE, $hiddenFile);
        File::copy(self::EXAMPLE_FILE, $subDirFile);

        $contentWithHiddenFiles = File::readDirectory(self::EXAMPLE_DIRECTORY, true);

        self::assertEquals(
            [
                'test_file.txt' => '/var/cache/test_dir/inside/last_dir/test_file.txt',
                'test_file_copy.txt' => '/var/cache/test_dir/inside/last_dir/test_file_copy.txt',
                '.hidden_file.txt' => '/var/cache/test_dir/inside/last_dir/.hidden_file.txt',
                'sub_directory' => '/var/cache/test_dir/inside/last_dir/sub_directory',
            ],
            $contentWithHiddenFiles
        );

        $contentWithoutHiddenFiles = File::readDirectory(self::EXAMPLE_DIRECTORY, false);

        self::assertEquals(
            [
                'test_file.txt' => '/var/cache/test_dir/inside/last_dir/test_file.txt',
                'test_file_copy.txt' => '/var/cache/test_dir/inside/last_dir/test_file_copy.txt',
                'sub_directory' => '/var/cache/test_dir/inside/last_dir/sub_directory',
            ],
            $contentWithoutHiddenFiles
        );

        $contentWithHiddenFilesAndSubDir = File::readDirectory(self::EXAMPLE_DIRECTORY, true, true);

        self::assertEquals(
            [
                'test_file.txt' => '/var/cache/test_dir/inside/last_dir/test_file.txt',
                'test_file_copy.txt' => '/var/cache/test_dir/inside/last_dir/test_file_copy.txt',
                '.hidden_file.txt' => '/var/cache/test_dir/inside/last_dir/.hidden_file.txt',
                'sub_directory' => [
                    'test.txt' => '/var/cache/test_dir/inside/last_dir/sub_directory/test.txt',
                ],
            ],
            $contentWithHiddenFilesAndSubDir
        );

        File::delete($hiddenFile);
        File::delete($subDirFile);
        File::delete(self::EXAMPLE_DIRECTORY . '/sub_directory');
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
