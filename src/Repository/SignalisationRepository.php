<?php

namespace App\Repository;

use App\Entity\Signalisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Signalisation>
 *
 * @method Signalisation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Signalisation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Signalisation[]    findAll()
 * @method Signalisation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SignalisationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Signalisation::class);
    }

    public function add(Signalisation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Signalisation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findUserSignalee(){
        $query = $this->createQueryBuilder("s");
        $query->innerJoin("s.utilisateurSignale","u")
            ->addSelect("u");
        $query->andWhere("s.etat LIKE :etat")
        ->setParameter("etat","en attente");
        $query->orderBy("s.dateSignalement","ASC");

       return $query->getQuery()->getResult();
    }

}
