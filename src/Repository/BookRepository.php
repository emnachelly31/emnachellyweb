<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }



    public function findBooksSortedByAuthor()
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findBooksByAuthor($id)
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->where('a.id = :x')
            ->andWhere('b.publicationDate > :y')
            ->setParameter('x', $id)
            ->setParameter('y', '2023-01-01');
        return $qb->getQuery()
            ->getResult();
    }



    public function searchBook($value)
    {
        return $this->createQueryBuilder('b')
            ->where('b.title LIKE :title')
            ->setParameter('title', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }



    public function countPublishedBooks()
    {
        return $this->createQueryBuilder('b')
            ->select('COUNT(b.ref)')
            ->where('b.published = :published')
            ->setParameter('published', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countNotPublishedBooks()
    {
        return $this->createQueryBuilder('b')
            ->select('COUNT(b.ref)')
            ->where('b.published = :published')
            ->setParameter('published', false)
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function getSumOfBooksInCategory($category)
    {
        return $this->createQueryBuilder('b')
            ->select('SUM(b.quantity) as total')
            ->where('b.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
