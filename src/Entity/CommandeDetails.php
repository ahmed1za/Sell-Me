<?php

namespace App\Entity;

use App\Repository\CommandeDetailsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeDetailsRepository::class)
 */
class CommandeDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Commande::class, inversedBy="commandeDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Commande_id;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="commandeDetails")
     * @ORM\JoinColumn(nullable=false)
     */

    private $produit_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantite;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    public function __construct()
    {
        $this->produit_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommandeId(): ?Commande
    {
        return $this->Commande_id;
    }

    public function setCommandeId(?Commande $Commande_id): self
    {
        $this->Commande_id = $Commande_id;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduitId(): ?Produit
    {
        return $this->produit_id;
    }
    public function setProduitId(?Produit $produit_id) : self
    {
        $this->produit_id =$produit_id;
        return $this;
    }
    public function addProduitId(Produit $produitId): self
    {
        if (!$this->produit_id->contains($produitId)) {
            $this->produit_id[] = $produitId;
        }

        return $this;
    }

    public function removeProduitId(Produit $produitId): self
    {
        $this->produit_id->removeElement($produitId);

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

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }
}
