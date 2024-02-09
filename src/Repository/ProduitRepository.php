<?php

namespace App\Repository;

use App\Data\Filtre;
use App\Entity\Produit;
use App\Form\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    private $paginator;
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Produit::class);
        $this->paginator = $paginator;
    }

    public function add(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Produit[] Returns an array of Produit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function findBestProduct(int $page = 1, int $limit = 30){
    $queryBuilder = $this->createQueryBuilder("p");

    $queryBuilder->leftJoin("p.images","pi")->addSelect("pi");

    $queryBuilder->andWhere("p.prix > 0");
    $queryBuilder->addOrderBy("p.prix","ASC");
    $query = $queryBuilder->getQuery();
    //$query->setMaxResults(30);
    //$paginator = new Paginator($query);
   // $result = $query->getResult();

    return $this->paginator->paginate($query,$page,$limit);
}


    public function findByCategory($categorie,int $page = 1, int $limit = 30)
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder
            ->innerJoin('p.categorie', 'c')  // Utiliser 'categorie' au lieu de 'categories'
            ->where('c.nom = :categorie')
            ->leftJoin('p.images','i')
            ->addSelect('i')
            ->setParameter('categorie', $categorie)
            ->orderBy('p.prix', 'ASC');

        $query = $queryBuilder->getQuery();

        return $this->paginator->paginate($query,$page,$limit);
    }
    // ProduitRepository.php

    public function searchProduct($nom,$categorie)
    {
        $queryBuilder = $this->createQueryBuilder('p');

        if ($nom) {
            $queryBuilder
                ->andWhere('p.nom LIKE :nom')
                ->setParameter('nom', '%' . $nom . '%')
                ->leftJoin('p.images','i')
                ->addSelect('i')
            ;
        }

        if ($categorie) {
            $queryBuilder
                ->andWhere('p.categorie = :categorie')
                ->setParameter('categorie', $categorie);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function filtrer(Filtre $filtre, int $page = 1, int $limit = 30) {
        $query= $this->createQueryBuilder('p')
            ->select('c','p')
            ->join('p.categorie','c')
            ->leftJoin('p.images','i')
            ->addSelect('i')
            ->join('p.Vendeur', 'u')
            ->addSelect('u')
        ;

        if (!empty($filtre->min)){
           $query = $query->andWhere('p.prix >= :min')
                ->setParameter('min', $filtre->min);
        }
        if (!empty($filtre->max)){
            $query = $query->andWhere('p.prix <= :max')
                ->setParameter('max', $filtre->max);
        }
        if (!empty($filtre->livraison)){
           $query = $query->andWhere('p.livraison = 1');
        }
        if (!empty($filtre->etat)){
            $query = $query->andWhere('p.etat LIKE :etat')
                ->setParameter('etat', $filtre->etat);
        }

        if (!empty($filtre->nature)){
            $query = $query->andWhere('u.nature IN (:natures)')
                ->setParameter('natures', [$filtre->nature]);

        }

        if (!empty($filtre->categories)){
            $query = $query->andWhere('c.id IN (:categories)')
                ->setParameter('categories',$filtre->categories);
        }

        return $this->paginator->paginate($query,$page,$limit);


    }



}
