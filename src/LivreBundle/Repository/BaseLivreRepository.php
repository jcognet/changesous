<?php

namespace LivreBundle\Repository;
use LivreBundle\Entity\BaseLivre;

/**
 * BaseLivreRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BaseLivreRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Retourne une query renvoyant des livres
     * @return \Doctrine\ORM\Query
     */
    public function getQueryListeLivre()
    {
        return $this->createQueryBuilder('l')
            ->addOrderBy('l.dateCreation', 'DESC')
            ->getQuery();
    }

    /**
     * Retourne un livre à partir de son isbn
     * @param $isbn
     * @return BaseLivre |null
     */
    public function findOneByIsbn($isbn){
        return $this->createQueryBuilder('l')
            ->where('l.isbn10 = :isbn OR l.isbn13 = :isbn')
            ->setParameter('isbn', $isbn)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
