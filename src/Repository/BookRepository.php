<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
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

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Book $entity, bool $flush = true): void
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
    public function remove(Book $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Récupére tout les livres dont le prix est entre
     * le minimum et maximum spécifié en paramètre
     */
    public function findByPriceBetween(float $minimum, float $maximum): array
    {
        return $this
            ->createQueryBuilder('book')
            ->orderBy('book.price', 'ASC')
            ->andWhere('book.price >= :min')
            ->andWhere('book.price <= :max')
            ->setParameter('min', $minimum)
            ->setParameter('max', $maximum)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des livres par nom d'auteur
     */
    public function findByAuthorName(string $authorName): array
    {
        return $this
            ->createQueryBuilder('book')
            ->leftJoin('book.author', 'author')
            ->andWhere('author.name LIKE :name')
            ->setParameter('name', "%$authorName%")
            ->orderBy('book.price', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des livres par leurs catégorie
     */
    public function findByCategoryName(string $categoryName): array
    {
        return $this
            ->createQueryBuilder('book')
            ->leftJoin('book.categories', 'category')
            ->andWhere('category.title LIKE :category')
            ->setParameter('category', "%$categoryName%")
            ->orderBy('book.price', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
