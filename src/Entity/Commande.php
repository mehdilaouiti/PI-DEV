<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")

     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)

     */
    private $prixTotal;


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="Commandes")

     */
    private $emailuser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message=" Adresse devrait etre non vide")
     */
    private $adresse;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message=" Adresse devrait etre non vide")

     */
    private $nom;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message=" Adresse devrait etre non vide")

     */
    private $prenom;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Description Adresse devrait etre non vide")

     *
     */
    private $descriptionAdresse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Gouvernorat devrait etre non vide")

     */
    private $gouvernorat;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *     type="integer",
     *      message="le code postal doit etre écrit par des chiffres uniquement")
     *
     * @Assert\Length(
     *     min=4,
     *     max=4,
     *     minMessage="Le code postal doit etre composé par 4 chiffres",
     *     maxMessage="Le code postal doit etre composé par 4 chiffres"
     * )
     * @Assert\NotBlank(message="Code Postal devraitt etre non vide")

     */
    private $codePostal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(message="Numero Telephone devrait etre non vide")
     * @Assert\Length(
     *     min=12,
     *     max=12,
     *     minMessage="Le numéro téléphone doit etre composé par 8 chiffres",
     *     maxMessage="Le numéro téléphone doit etre composé par 8 chiffres"
     * )
     * @Assert\Type(
     *     type="integer",
     *      message="le numéro téléphone doit etre écrit par des chiffres uniquement")

     */
    private $numeroTelephone;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class)

     */
    private $Articles;
    /**
        * @ORM\OneToMany(targetEntity=Livraison::class, mappedBy="Commande")
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

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getPrixTotal(): ?float
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(float $Prixtotal): self
    {
        $this->prixTotal = $Prixtotal;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }
    public function getNom(): ?string
    {
        return $this->nom;
    }
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDescriptionAdresse(): ?string
    {
        return $this->descriptionAdresse;
    }

    public function setDescriptionAdresse(?string $descriptionadresse): self
    {
        $this->descriptionAdresse = $descriptionadresse;

        return $this;
    }

    public function getGouvernorat(): ?string
    {
        return $this->gouvernorat;
    }

    public function setGouvernorat(string $gouvernorat): self
    {
        $this->gouvernorat = $gouvernorat;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(int $codepostal): self
    {
        $this->codePostal = $codepostal;

        return $this;
    }

    public function getNumeroTelephone(): ?int
    {
        return $this->numeroTelephone;
    }

    public function setNumeroTelephone(int $numerotelephone): self
    {
        $this->numeroTelephone = $numerotelephone;

        return $this;
    }

    public function getArticles(): ?Article
    {
        return $this->Articles;
    }

    public function setArticles(?Article $Articles): self
    {
        $this->Articles = $Articles;

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
            $livraison->setCommande($this);
        }

        return $this;
    }

    public function __toString()
    {
        return(string)$this->getId();
    }

    public function getEmailuser(): ?User
    {
        return $this->emailuser;
    }

    public function setEmailuser(?User $emailuser): self
    {
        $this->emailuser = $emailuser;

        return $this;
    }



}






