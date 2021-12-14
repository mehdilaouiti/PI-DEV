<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups("post:read")
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank()
     * @Groups("post:read")
     */
    private $prix;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Groups("post:read")
     */
    private $quantite;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=4,max=30)
     * @Groups("post:read")
     */
    private $nom;

    /**
     *  @var  string
     * @ORM\Column(name="img",type="string", length=255)
     * @Assert\NotBlank()
     * @Groups("post:read")
     */
    private $img;

    /**
     * @ORM\ManyToMany(targetEntity=Panier::class, inversedBy="articles")
     */
    private $paniers;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="articles")
     * @Groups("post:read")
     */
    private $categorie;

    /**
     * @ORM\ManyToOne(targetEntity=Fabricant::class, inversedBy="articles")
     * @Groups("post:read")
     */
    private $fabricant;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="favoris")
     * @Groups("post:read")
     */
    private $favoris;

    /**
     * @ORM\OneToMany(targetEntity=Like::class, mappedBy="art")
     * @Groups("post:read")
     */
    private $likes;
    /**
     * @ORM\ManyToMany(targetEntity=Promotion::class, mappedBy="articles")
     */
    private $promotions;

    public function __construct()
    {
        $this->paniers = new ArrayCollection();
        $this->favoris = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->promotions = new ArrayCollection();

    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return string
     */
    public function getImg(): ?string
    {
        return $this->img;
    }
    /**
     * @param string $img
     */

    public function setImg(string $img): void
    {
        $this->img = $img;
    }

    /**
     * @return Collection|Panier[]
     */
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function addPanier(Panier $panier): self
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers[] = $panier;
        }

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        $this->paniers->removeElement($panier);

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }
    public function __toString()
    {
        return (string)$this->getNom();

    }

    public function getFabricant(): ?Fabricant
    {
        return $this->fabricant;
    }

    public function setFabricant(?Fabricant $fabricant): self
    {
        $this->fabricant = $fabricant;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(User $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris[] = $favori;
        }

        return $this;
    }

    public function removeFavori(User $favori): self
    {
        $this->favoris->removeElement($favori);

        return $this;
    }

    /**
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setArt($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getArt() === $this) {
                $like->setArt(null);
            }
        }

        return $this;
    }

    /**
     * @param \App\Entity\User $user
     * @return bool
     */


    public function isLikedByUser(User $user) :bool
    {
        foreach ($this->likes as $like){
            if($like->getUser() == $user) return true;

        }
        return false;
    }
    /**
     * @return Collection|Promotion[]
     */
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    public function addPromotion(Promotion $promotion): self
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions[] = $promotion;
            $promotion->addArticle($this);
        }

        return $this;
    }

    public function removePromotion(Promotion $promotion): self
    {
        if ($this->promotions->removeElement($promotion)) {
            $promotion->removeArticle($this);
        }

        return $this;
    }

}
