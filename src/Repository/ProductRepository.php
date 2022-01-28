<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Ulid;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function byCategoryIdAndLocale(Ulid $categoryId, string $locale): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('pd')
            ->addSelect('c')
            ->innerJoin('p.productDetails', 'pd')
            ->leftJoin('p.category', 'c')
            ->andWhere('p.category = :categoryId')
            ->andWhere('pd.language.value = :locale')
            ->setParameter('categoryId', $categoryId->toBinary())
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getArrayResult();
    }

    public function byLocale(string $locale): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('pd')
            ->addSelect('c')
            ->innerJoin('p.productDetails', 'pd')
            ->leftJoin('p.category', 'c')
            ->andWhere('pd.language.value = :locale')
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getArrayResult();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
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
