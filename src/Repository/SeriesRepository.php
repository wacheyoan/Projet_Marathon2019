<?php

namespace App\Repository;

use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Series|null find($id, $lockMode = null, $lockVersion = null)
 * @method Series|null findOneBy(array $criteria, array $orderBy = null)
 * @method Series[]    findAll()
 * @method Series[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Series::class);
    }

    public function findRandoms(){

        $raw_sql = "
            SELECT * 
            FROM series
            WHERE score >= 8
            ORDER BY RAND() 
            LIMIT 3
        ";

        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($raw_sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function findMostLiked(){

        $raw_sql = "
            SELECT *
            FROM series
            WHERE score = (
                SELECT max(score) 
                FROM series
                )         
        ";
        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($raw_sql);
        $query->execute();

        return $query->fetch();
    }

}
