<?php

namespace App\Repository;

use App\Entity\Episodes;
use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Series|null find($id, $lockMode = null, $lockVersion = null)
 * @method Series|null findOneBy(array $criteria, array $orderBy = null)
 * @method Series[]    findAll()
 * @method Series[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpisodesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Episodes::class);
    }

    public function findAllOrderedByDatePaginated($page,$maxResult){

        if (!is_numeric($page)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument $page est incorrecte (valeur : ' . $page . ').'
            );
        }

        if($page < 1){
            throw new NotFoundHttpException('La page demandée n\'existe pas');
        }

        if (!is_numeric($maxResult)) {
            throw new InvalidArgumentException(
                'La valeur de l\'argument $nbMaxParPage est incorrecte (valeur : ' . $maxResult . ').'
            );
        }

        $firstResult = ($page - 1) * $maxResult;
        $date = new \DateTime('now');

        $qb = $this->createQueryBuilder('e')
            ->orderBy('e.premiere','DESC')
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResult)
            ;

        $pagination = new Paginator($qb);

        if(($pagination->count() <= $firstResult) && $page !=1){
            throw new NotFoundHttpException('La page demandée n\'existe pas.');
        }

        return $pagination;
    }

}
