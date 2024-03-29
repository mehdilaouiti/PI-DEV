<?php

namespace App\Entity;

use App\Repository\LivreurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=LivreurRepository::class)
 */
class Livreur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     *
     */

    private $NomLiv;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $PrenomLiv;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Assert\Length(
     *      min = 8,
     *      max = 8,
     *      minMessage = "le numero doit etre compose de 8 chiffres",
     *      maxMessage = "le numero doit etre compose de 8 chiffres"
     * )
     *
     * @Assert\NotBlank()
     */
    private $numLiv;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity=Livraison::class, mappedBy="Livreur")
     */
    private $livraisons;



    public function __construct()
    {
        $this->livraisons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomLiv(): ?string
    {
        return $this->NomLiv;
    }

    public function setNomLiv(string $NomLiv): self
    {
        $this->NomLiv = $NomLiv;

        return $this;
    }

    public function getPrenomLiv(): ?string
    {
        return $this->PrenomLiv;
    }

    public function setPrenomLiv(string $PrenomLiv): self
    {
        $this->PrenomLiv = $PrenomLiv;

        return $this;
    }

    public function getNumLiv(): ?string
    {
        return $this->numLiv;
    }

    public function setNumLiv(string $numLiv): self
    {
        $this->numLiv = $numLiv;

        return $this;
    }

    public function getimage(): ?string
    {
        return $this->image;
    }

    public function setimage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Livraison[]
     */
    public function getLivraisons(): Collection
    {
        return $this->livraisons;
    }

    public function addLivraison(Livraison $livraison): self
    {
        if (!$this->livraisons->contains($livraison)) {
            $this->livraisons[] = $livraison;
            $livraison->setLivreur($this);
        }

        return $this;
    }

    public function removeLivraison(Livraison $livraison): self
    {
        if ($this->livraisons->removeElement($livraison)) {
            // set the owning side to null (unless already changed)
            if ($livraison->getLivreur() === $this) {
                $livraison->setLivreur(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return(string)$this->getNomLiv();
    }





}
