<?php

namespace App\Entity;

use App\Repository\PromotionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PromotionRepository::class)
 */
class Promotion
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
     * @Assert\NotBlank(
     *     message="Cette valeur ne doit pas être vide."
     * )
     * @Groups("post:read")
     */
    private $nompromotion;


    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(
     *     message="Cette valeur ne doit pas être vide."
     * )
     * @Groups("post:read")
     */
    private $remise;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Cette valeur ne doit pas être vide."
     * )
     * @Groups("post:read")
     */
    private $description;
    /**
     * @var  string
     * @ORM\Column(name="img",type="string", length=255)
     * @Assert\NotBlank(
     *     message="Cette valeur ne doit pas être vide."
     * )
     * @Groups("post:read")
     */
    private $img;

    /**
     * @ORM\ManyToMany(targetEntity=Article::class, inversedBy="promotions")
     * @Groups("post:read")
     */
    private $articles;

    /**
     * @ORM\Column(type="date")
     * @Groups("post:read")
     */
    private $date;



    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getNompromotion(): ?string
    {
        return $this->nompromotion;
    }

    public function setNompromotion(string $nompromotion): self
    {
        $this->nompromotion = $nompromotion;

        return $this;
    }



    public function getRemise(): ?float
    {
        return $this->remise;
    }

    public function setRemise(float $remise): self
    {
        $this->remise = $remise;

        return $this;
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
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        $this->articles->removeElement($article);

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }








}
