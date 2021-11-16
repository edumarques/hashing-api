<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractCustomHash
{
    public const MD5 = 'md5';

    protected ?string $algorithm = null;

    protected ?string $hash = null;

    protected ?string $key = null;

    protected ?int $attempts = null;


    public function getHash(): string
    {
        return $this->hash;
    }


    public function setHash(string $hash): self
    {
        $this->hash = $hash;
        return $this;
    }


    /**
     * @return string|null
     */
    public function getAlgorithm(): ?string
    {
        return $this->algorithm;
    }


    /**
     * @param string|null $algorithm
     *
     * @return AbstractCustomHash
     */
    public function setAlgorithm(?string $algorithm): AbstractCustomHash
    {
        $this->algorithm = $algorithm;
        return $this;
    }


    public function getKey(): ?string
    {
        return $this->key;
    }


    public function setKey(?string $key): self
    {
        $this->key = $key;
        return $this;
    }


    public function getAttempts(): ?int
    {
        return $this->attempts;
    }


    public function setAttempts(?int $attempts): self
    {
        $this->attempts = $attempts;
        return $this;
    }
}