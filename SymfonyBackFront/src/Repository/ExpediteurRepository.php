<?php

namespace App\Repository;

use App\Entity\Expediteur;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

/**
 * @method Expediteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expediteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expediteur[]    findAll()
 * @method Expediteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpediteurRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expediteur::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Expediteur $entity, bool $flush = true): void
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
    public function remove(Expediteur $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Expediteur[] Returns an array of Expediteur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Expediteur
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findAllWithoutClient()
    {
        return $this->_em->createQuery('
        SELECT expediteur
        FROM App\Entity\Expediteur expediteur
        WHERE expediteur.client IS NULL
        ')->getResult();
    }

    public function findLike($nom)
    {
        // return $this->_em->createQuery('
        // SELECT expediteur
        // FROM App\Entity\Expediteur expediteur
        // WHERE expediteur.nom LIKE :nom
        // ')->setParameter('nom', '%' . $nom . '%');

        return $this->_em->createQueryBuilder('e')
            ->select('
                e.id,
                e.email,
                e.nom,
                e.prenom,
                e.roles,
                e.createdAt,
                e.updatedAt,
                client.raisonSociale AS raisonSociale
            ')
            ->from('App\Entity\Expediteur', 'e')
            ->where('e.nom LIKE :nom OR e.prenom LIKE :nom OR client.raisonSociale LIKE :nom')
            ->leftJoin('e.client', 'client')
            ->setParameter('nom', '%' . $nom . '%')
            ->getQuery()
            ->getResult();
    }

    public function findAllInactive($role = 'ROLE_INACTIF')
    {
        $qb = $this->_em->createQueryBuilder('e')
            ->select('
                e.id,
                e.email,
                e.nom,
                e.prenom,
                e.roles,
                client.raisonSociale AS raisonSociale,
                LENGTH(e.password) AS pswLength,
                e.createdAt
            ')
            ->from('App\Entity\Expediteur', 'e')
            ->where('e.roles LIKE :role')
            ->leftJoin('e.client', 'client')
            ->setParameter('role', '%' . $role . '%')
            ->getQuery();

        return $qb->getResult();
    }

    public function FindExpediteurLastMonths($nbMonths, $role)
    {
        $dateToSearch = (new DateTime('now'))->modify('-' . $nbMonths . ' hours');
        $qb = $this->_em->createQueryBuilder('e')
            ->select('
                e.createdAt
            ')
            ->from('App\Entity\Expediteur', 'e')
            ->where('e.createdAt >= :dateToSearch')
            ->andWhere('e.roles LIKE :role')
            ->setParameter('dateToSearch', $dateToSearch)
            ->setParameter('role', '%' . $role . '%')
            ->getQuery();
        return $qb->getResult();
    }

    public function findExpediteurToKeep(DateTime $date)
    {
        $qb = $this->_em->createQueryBuilder('e')
            ->select('
                e.id as expediteurId
            ')
            ->from('App\Entity\Expediteur', 'e')
            ->leftJoin('e.courriers', 'c')
            ->leftJoin('c.statutsCourrier', 'sc')
            ->where('(sc.date >= :dateMin AND sc.date <= :dateMax) OR e.createdAt >= :dateMin')
            ->groupBy('e.id')
            ->setParameter('dateMin', date_modify(new DateTime($date->format('Y-m-d')), '-6 month'))
            ->setParameter('dateMax', $date)
            ->getQuery();

        return $qb->getResult();
    }
}
