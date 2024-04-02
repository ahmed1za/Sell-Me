<?php

namespace App\Repository;

use App\Entity\Messages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Messages>
 *
 * @method Messages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Messages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Messages[]    findAll()
 * @method Messages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagesRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messages::class);

    }

    public function add(Messages $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Messages $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findConversation($envoyeur, $destinataire){

        $queryBuilder = $this->createQueryBuilder("m");
        $queryBuilder->join("m.produit","mp")->addSelect("mp");
        $queryBuilder->andWhere("m.envoyeur = :envoyeur Or m.destinataire = :destinataire ");
        $queryBuilder->orWhere("m.envoyeur = :destinataire Or m.destinataire = :envoyeur ");
        $queryBuilder->setParameter("envoyeur", $envoyeur);
        $queryBuilder->setParameter("destinataire",$destinataire);
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    public function findMessages($envoyeur, $destinataire) {
        $queryBuilder = $this->createQueryBuilder("m");
        $queryBuilder->join("m.produit", "mp");
        $queryBuilder->where(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('m.envoyeur', ':envoyeur'),
                    $queryBuilder->expr()->eq('m.destinataire', ':destinataire')
                ),
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('m.envoyeur', ':destinataire'),
                    $queryBuilder->expr()->eq('m.destinataire', ':envoyeur')
                )
            )
        );
        $queryBuilder->setParameter('envoyeur', $envoyeur);
        $queryBuilder->setParameter('destinataire', $destinataire);
        $queryBuilder->orderBy("m.dateDeCreation","ASC");

        return $queryBuilder->getQuery()->getResult();
    }


    public function unreadMessageCount($destinataire){
       $queryBuilder =  $this->createQueryBuilder('m')
           ->select('COUNT(m.id)')
           ->andWhere('m.destinataire = :destinataire')
           ->andWhere('m.est_lu = :est_lu')
           ->setParameter('destinataire', $destinataire)
           ->setParameter('est_lu',false);

          return $queryBuilder->getQuery()->getResult();
    }

//    public function findOneBySomeField($value): ?Messages
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
