<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Produits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;


/**
 * @method Produits|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produits|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produits[]    findAll()
 * @method Produits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitsRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Produits::class);
        $this->paginator = $paginator;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Produits $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Produits $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    /**
     * Récupère les produits en lien avec une recherche
     * @return PaginationInterface
     */
    public function findSearch(SearchData $search): PaginationInterface
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.category', 'c');

            if(!empty($search->q)){
                $query = $query
                ->andWhere('p.titre LIKE :q')
                ->setParameter('q', "%{$search->q}%");
            }
            if(!empty($search->min)){
                $query = $query
                ->andWhere('p.prix >= :min')
                ->setParameter('min', $search->min);
            }
            if(!empty($search->max)){
                $query = $query
                ->andWhere('p.prix <= :max')
                ->setParameter('max', $search->max);
            }
            if(!empty($search->categories)){
                $query = $query
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->categories);
            }

        $query = $query->getQuery();
        return $this->paginator->paginate($query, $search->page, 6);
            
    }

    // /**
    //  * @return Produits[] Returns an array of Produits objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Produits
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
