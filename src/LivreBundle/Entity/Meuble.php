<?php

namespace LivreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Meuble
 *
 * @ORM\Table(name="meuble")
 * @ORM\Entity(repositoryClass="LivreBundle\Repository\MeubleRepository")
 */
class Meuble
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var \LivreBundle\Entity\Piece
     *
     * @ORM\ManyToOne(targetEntity="LivreBundle\Entity\Piece", inversedBy="meubles")
     */
    private $piece;


    /**
     * @var \LivreBundle\Entity\Etagere
     *
     * @ORM\OneToMany(targetEntity="LivreBundle\Entity\Etagere", mappedBy="meuble")
     */
    private $etageres;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Meuble
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->etageres = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set piece
     *
     * @param \LivreBundle\Entity\Piece $piece
     *
     * @return Meuble
     */
    public function setPiece(\LivreBundle\Entity\Piece $piece = null)
    {
        $this->piece = $piece;

        return $this;
    }

    /**
     * Get piece
     *
     * @return \LivreBundle\Entity\Piece
     */
    public function getPiece()
    {
        return $this->piece;
    }

    /**
     * Add etagere
     *
     * @param \LivreBundle\Entity\Etagere $etagere
     *
     * @return Meuble
     */
    public function addEtagere(\LivreBundle\Entity\Etagere $etagere)
    {
        $this->etageres[] = $etagere;

        return $this;
    }

    /**
     * Remove etagere
     *
     * @param \LivreBundle\Entity\Etagere $etagere
     */
    public function removeEtagere(\LivreBundle\Entity\Etagere $etagere)
    {
        $this->etageres->removeElement($etagere);
    }

    /**
     * Get etageres
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEtageres()
    {
        return $this->etageres;
    }
}
