<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository;

use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\HashingBatchRepository;
use PHPUnit\Framework\MockObject\MockObject;

final class HashingBatchRepositoryTest extends TestCase
{
    private MockObject $entityManager;

    private MockObject $classMetadata;

    private HashingBatchRepository $hashingBatchRepository;


    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->classMetadata = $this->createMock(ClassMetadata::class);

        $this->hashingBatchRepository = new HashingBatchRepository($this->entityManager, $this->classMetadata);
    }


    public function testQueryAll(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query        = $this->createMock(AbstractQuery::class);

        $this->entityManager->expects(self::once())->method('createQueryBuilder')->willReturn($queryBuilder);
        $queryBuilder->expects(self::once())->method('select')->with('o')->willReturnSelf();
        $queryBuilder->expects(self::once())->method('from')->willReturnSelf();
        $queryBuilder->expects(self::once())->method('getQuery')->willReturn($query);

        self::assertSame($query, $this->hashingBatchRepository->queryAll());
    }


    public function testQueryByAttemptsLessThan(): void
    {
        $attempts     = rand(1, 999999);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query        = $this->createMock(AbstractQuery::class);

        $this->entityManager->expects(self::once())->method('createQueryBuilder')->willReturn($queryBuilder);
        $queryBuilder->expects(self::once())->method('select')->with('o')->willReturnSelf();
        $queryBuilder->expects(self::once())->method('from')->willReturnSelf();
        $queryBuilder->expects(self::once())->method('where')->with("o.attempts < $attempts")->willReturnSelf();
        $queryBuilder->expects(self::once())->method('getQuery')->willReturn($query);

        self::assertSame($query, $this->hashingBatchRepository->queryByAttemptsLessThan($attempts));
    }
}