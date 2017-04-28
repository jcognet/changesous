<?php

namespace LivreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Livre
 *
 * @ORM\Table(name="livre")
 * @ORM\Entity(repositoryClass="LivreBundle\Repository\LivreRepository")
 */
class Livre
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateAjout", type="datetime")
     */
    private $dateAjout;

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float", nullable=true)
     */
    private $prix;

    /**
     * @var \UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="listeLivres")
     */
    private $proprietaire;


    /**
     * @var \LivreBundle\Entity\BaseLivre
     *
     * @ORM\ManyToOne(targetEntity="LivreBundle\Entity\BaseLivre")
     */
    private $baseLivre;


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
     * Set dateAjout
     *
     * @param \DateTime $dateAjout
     *
     * @return Livre
     */
    public function setDateAjout($dateAjout)
    {
        $this->dateAjout = $dateAjout;

        return $this;
    }

    /**
     * Get dateAjout
     *
     * @return \DateTime
     */
    public function getDateAjout()
    {
        return $this->dateAjout;
    }

    /**
     * Set dateAction
     *
     * @param \DateTime $dateAction
     *
     * @return Livre
     */
    public function setDateAction($dateAction)
    {
        $this->dateAction = $dateAction;

        return $this;
    }

    /**
     * Get dateAction
     *
     * @return \DateTime
     */
    public function getDateAction()
    {
        return $this->dateAction;
    }

    /**
     * Set action
     *
     * @param string $action
     *
     * @return Livre
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set prix
     *
     * @param float $prix
     *
     * @return Livre
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return float
     */
    public function getPrix()
    {
        return $this->prix;
    }

    public function __construct()
    {
        $this->dateAjout = new \DateTime();
    }

    /**
     * Set proprietaire
     *
     * @param \UserBundle\Entity\User $proprietaire
     *
     * @return Livre
     */
    public function setProprietaire(\UserBundle\Entity\User $proprietaire = null)
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    /**
     * Get proprietaire
     *
     * @return \UserBundle\Entity\User
     */
    public function getProprietaire()
    {
        return $this->proprietaire;
    }

    /**
     * Set baseLivre
     *
     * @param \LivreBundle\Entity\BaseLivre $baseLivre
     *
     * @return Livre
     */
    public function setBaseLivre(\LivreBundle\Entity\BaseLivre $baseLivre = null)
    {
        $this->baseLivre = $baseLivre;

        return $this;
    }

    /**
     * Get baseLivre
     *
     * @return \LivreBundle\Entity\BaseLivre
     */
    public function getBaseLivre()
    {
        return $this->baseLivre;
    }
}