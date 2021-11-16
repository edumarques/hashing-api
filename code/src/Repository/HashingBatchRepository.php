<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;

class HashingBatchRepository extends EntityRepository
{
    public function queryAll(): AbstractQuery
    {
        return $this->createQueryBuilder('o')->getQuery();
    }

    public function queryByAttemptsLessThan(int $attempts): AbstractQuery
    {
        return $this->createQueryBuilder('o')
            ->where("o.attempts < ${attempts}")
            ->getQuery();
    }
}
