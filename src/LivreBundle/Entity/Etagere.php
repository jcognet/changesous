<?php

namespace LivreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Etagere
 *
 * @ORM\Table(name="etagere")
 * @ORM\Entity(repositoryClass="LivreBundle\Repository\EtagereRepository")
 */
class Etagere
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
     * @var \LivreBundle\Entity\Meuble
     *
     * @ORM\ManyToOne(targetEntity="LivreBundle\Entity\Meuble", inversedBy="etageres")
     */
    private $meuble;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}

