<?php

namespace App\Entity;

use App\Repository\SignalisationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SignalisationRepository::class)
 */
class Signalisation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userSignale")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateurSignale;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userQuiSignal")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateurQuiSignale;

    /**
     * @ORM\Column(type="string")
     */
    private $raison;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateSignalement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $actionPrise;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $motif;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="signalisations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $produit;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accesMessage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateurSignale(): ?User
    {
        return $this->utilisateurSignale;
    }

    public function setUtilisateurSignale(?User $utilisateurSignale): self
    {
        $this->utilisateurSignale = $utilisateurSignale;

        return $this;
    }

    public function getUtilisateurQuiSignale(): ?User
    {
        return $this->utilisateurQuiSignale;
    }

    public function setUtilisateurQuiSignale(?User $utilisateurQuiSignale): self
    {
        $this->utilisateurQuiSignale = $utilisateurQuiSignale;

        return $this;
    }

    public function getRaison(): ?string
    {
        return $this->raison;
    }

    public function setRaison(string $raison): self
    {
        $this->raison = $raison;

        return $this;
    }

    public function getDateSignalement(): ?\DateTimeInterface
    {
        return $this->dateSignalement;
    }

    public function setDateSignalement(\DateTimeInterface $dateSignalement): self
    {
        $this->dateSignalement = $dateSignalement;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getActionPrise(): ?string
    {
        return $this->actionPrise;
    }

    public function setActionPrise(?string $actionPrise): self
    {
        $this->actionPrise = $actionPrise;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function isAccesMessage(): ?bool
    {
        return $this->accesMessage;
    }

    public function setAccesMessage(bool $accesMessage): self
    {
        $this->accesMessage = $accesMessage;

        return $this;
    }
}
