<?php

namespace App\Entity;

use App\Repository\LivraisonRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=LivraisonRepository::class)
 */
class Livraison
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     *  @Assert\NotBlank()
     * @Groups("post:read")
     */
    private $DateLiv;

    /**
     * @ORM\ManyToOne(targetEntity=Livreur::class, inversedBy="livraisons")
     * @ORM\JoinColumn(nullable=false)
     *  @Assert\NotBlank()
     * @Groups("post:read")
     */
    private $Livreur;

    /**
     * @ORM\ManyToOne(targetEntity=Commande::class, inversedBy="livraisons", cascade={"persist", "remove"})
     *@ORM\JoinColumn(nullable=true , referencedColumnName="id")
     * @Assert\NotBlank()
     * @Groups("post:read")
     */
    public $Commande;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("post:read")
     */
    private $Statut;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateLiv(): ?DateTimeInterface
    {
        return $this->DateLiv;
    }

    public function setDateLiv(DateTimeInterface $DateLiv): self
    {
        $this->DateLiv = $DateLiv;

        return $this;
    }

    public function getLivreur(): ?Livreur
    {
        return $this->Livreur;
    }

    public function setLivreur(?Livreur $Livreur): self
    {
        $this->Livreur = $Livreur;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->Commande;
    }

    public function setCommande(?Commande $Commande): self
    {
        $this->Commande = $Commande;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->Statut;
    }

    public function setStatut(?string $Statut): self
    {
        $this->Statut = $Statut;

        return $this;
    }



}
