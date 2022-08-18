<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Serie>
 *
 * @method Serie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Serie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Serie[]    findAll()
 * @method Serie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }

    public function add(Serie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Serie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


        // ##########création de nos methodes de recherches dans BDD
    public function findBestSeries(){
        // // en DQL
// $entityManager = $this->getEntityManager(); on ne peut pas récuperer entityManager par l'argument
// $dql = "
// SELECT s
// FROM App\Entity\Serie s
// WHERE s.popularity > 100
// AND s.vote > 8
// ORDER BY s.popularity DESC
// ";
// $query = $entityManager->createQuery($dql); création d'un objet query à partir de cette requette DQL


        // version QueryBuilder
        $queryBuilder = $this->createQueryBuilder('s');

        $queryBuilder->leftJoin('s.seasons','seas')
            ->addSelect('seas');
        $queryBuilder->andWhere('s.popularity > 100');
        $queryBuilder->andWhere('s.vote > 8');
        $queryBuilder->addOrderBy('s.popularity','DESC');
        $query = $queryBuilder->getQuery(); //création d'un objet query à partir de cette QUERYBUILDER

        //commun aux 2 façon de faire
        $query->setMaxResults(50); // filtres pour n'avoir qu'un certain nombre de résultats

        $paginator = new Paginator($query);

        // $results = $query->getResult(); // récupération des résultats sous forme de tableau pour envoie à la vue

     //   $results = $query->getOneOrNullResult(); si on sait qu'on va récuperer qu'un seul résultats

       // return $results; utilisation du paginator

        return $paginator;
    }


//    /**
//     * @return Serie[] Returns an array of Serie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Serie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
