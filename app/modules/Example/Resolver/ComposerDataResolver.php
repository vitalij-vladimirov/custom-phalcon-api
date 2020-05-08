<?php
declare(strict_types=1);

namespace Example\Resolver;

use Common\File;

class ComposerDataResolver
{
    private const COMPOSER_LOCATION = '/app/composer.lock';

    public function getComposerLockData(): string
    {
        return File::read(self::COMPOSER_LOCATION);
    }
}
