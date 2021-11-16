<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * @codeCoverageIgnore
 */
class CustomMd5Hash extends AbstractCustomHash
{
    protected ?string $algorithm = self::MD5;
}