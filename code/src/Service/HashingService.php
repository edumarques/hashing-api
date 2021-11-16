<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\AbstractCustomHash;
use App\DTO\CustomMd5Hash;
use App\Exception\HashingAlgorithmNotSupportedException;

class HashingService
{
    /**
     * @throws \Exception
     */
    public function generateCustomMd5(string $value): CustomMd5Hash
    {
        return $this->findHashWithPrefix($value, '0000');
    }

    /**
     * @throws \Exception
     */
    protected function findHashWithPrefix(
        string $value,
        string $prefix,
        string $algorithm = AbstractCustomHash::MD5
    ): ?AbstractCustomHash {
        $attempts = 0;

        do {
            $key = $this->generateRandomKey();
            $hash = $this->hash($value . $key, $algorithm);
            $attempts++;
        } while (! str_starts_with($hash, $prefix));

        return $this->getCustomHashObject($hash, $key, $algorithm, $attempts);
    }

    /**
     * @throws HashingAlgorithmNotSupportedException
     */
    protected function hash(string $value, string $algorithm): string
    {
        $this->validateHashingAlgorithm($algorithm);

        return match ($algorithm) {
            AbstractCustomHash::MD5 => md5($value),
        };
    }

    /**
     * @throws HashingAlgorithmNotSupportedException
     */
    protected function getCustomHashObject(
        string $hash,
        string $key,
        string $algorithm,
        int $attempts
    ): AbstractCustomHash {
        $this->validateHashingAlgorithm($algorithm);

        return match ($algorithm) {
            AbstractCustomHash::MD5 => (new CustomMd5Hash())->setHash($hash)->setKey($key)->setAttempts($attempts),
        };
    }

    /**
     * @throws HashingAlgorithmNotSupportedException
     */
    protected function validateHashingAlgorithm(string $algorithm): void
    {
        if ($algorithm !== AbstractCustomHash::MD5) {
            throw new HashingAlgorithmNotSupportedException();
        }
    }

    /**
     * @throws \Exception
     */
    protected function generateRandomKey(int $length = 8): string
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }
}
