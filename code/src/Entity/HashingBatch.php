<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @codeCoverageIgnore
 *
 * @ORM\Entity(repositoryClass="App\Repository\HashingBatchRepository")
 *
 * @ORM\Table(name="hashing_batch")
 */
class HashingBatch
{
    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue
     *
     * @ORM\Column()
     */
    protected int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected \DateTimeInterface $startDateTime;

    /**
     * @ORM\Column()
     */
    protected int $iteration;

    /**
     * @ORM\Column()
     */
    protected string $inputString;

    /**
     * @ORM\Column()
     */
    protected string $generatedKey;

    /**
     * @ORM\Column()
     */
    protected string $generatedHash;

    /**
     * @ORM\Column()
     */
    protected int $attempts;

    public function getId(): int
    {
        return $this->id;
    }

    public function getStartDateTime(): \DateTimeInterface
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(\DateTimeInterface $startDateTime): self
    {
        $this->startDateTime = $startDateTime;
        return $this;
    }

    public function getIteration(): int
    {
        return $this->iteration;
    }

    public function setIteration(int $iteration): self
    {
        $this->iteration = $iteration;
        return $this;
    }

    public function getInputString(): string
    {
        return $this->inputString;
    }

    public function setInputString(string $inputString): self
    {
        $this->inputString = $inputString;
        return $this;
    }

    public function getGeneratedKey(): string
    {
        return $this->generatedKey;
    }

    public function setGeneratedKey(string $generatedKey): self
    {
        $this->generatedKey = $generatedKey;
        return $this;
    }

    public function getGeneratedHash(): string
    {
        return $this->generatedHash;
    }

    public function setGeneratedHash(string $generatedHash): self
    {
        $this->generatedHash = $generatedHash;
        return $this;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function setAttempts(int $attempts): self
    {
        $this->attempts = $attempts;
        return $this;
    }
}
