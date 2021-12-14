<?php

namespace App\Entity;
use App\Repository\ReclamationRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ReclamationRepository::class)
 */
class Reclamation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     * @Groups("post:read")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=30)
     * @Groups("post:read")
     */
    private $description;

    /**
     * @Assert\DateTime()
     * @ORM\Column(type="datetime")
     */
    private $date_rec;
    /**
     *  @var  string
     * @ORM\Column(name="img",type="string", length=255)
     * @Groups("post:read")
     */
    private $img;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reclamations")
     * @Groups("post:read")
     */
    private $user;






    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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
     * @return DateTime
     */
    public function getDate_rec()
    {
        return $this->date_rec;
    }

    /**
     * @param DateTime $date_rec
     */
    public function setDate_rec(DateTime $date_rec)
    {
        $this->date_rec = $date_rec;
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








}