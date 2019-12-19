<?php

namespace App\Repository;

use App\Entity\Kind;
use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

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


    static function UniqueRandomNumbersWithinRange($min, $max, $quantity) {
        $numbers = range($min, $max);
        shuffle($numbers);
        return array_slice($numbers, 0, $quantity);
    }


    public function findRandoms(){

        $quantity = 3;


        $totalRowsTable = $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $random_ids = self::UniqueRandomNumbersWithinRange(1,$totalRowsTable,$quantity);


        return $this->createQueryBuilder('s')
            ->innerJoin('s.Kinds','k')
            ->andWhere('s.id IN (:ids)')
            ->setParameter('ids',$random_ids)
            ->getQuery()
            ->getResult()
            ;

       /* $raw_sql = "
            SELECT s.*,k.name as kind
            FROM series s
            INNER JOIN  series_kind sk
            ON sk.series_id = s.id
            INNER JOIN kind k
            ON k.id = sk.kind_id
            WHERE score >= 8
            ORDER BY RAND()
            LIMIT 3
        ";


        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($raw_sql);
        $query->execute();

        return $query->fetchAll();
       */
    }


    public function findByKind(String $kind){

        return $this->createQueryBuilder('s')
            ->innerJoin('s.Kinds','k')
            ->andWhere('k.name = (:kind)')
            ->setParameter('kind',$kind)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findMostLiked()
    {

        return $this->createQueryBuilder('s')
            ->setMaxResults(1)
            ->orderBy('s.score','DESC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
        /*$raw_sql = "
            SELECT s.*,k.name as kind
            FROM series s
            INNER JOIN  series_kind sk
            ON sk.series_id = s.id
            INNER JOIN kind k 
            ON k.id = sk.kind_id  
            WHERE score = (
                SELECT max(score) 
                FROM series
                )         
        ";
        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($raw_sql);
        $query->execute();

        return $query->fetch();*/
    }



}
