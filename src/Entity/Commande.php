<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommandeRepository")
 */
class Commande
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LigneDeCommande", mappedBy="commande", cascade={"persist"})
     */
    private $lignes_de_commande;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ModePaiement")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mode_paiement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StatutCommande")
     * @ORM\JoinColumn(nullable=false)
     */
    private $statut_commande;

    public function __construct()
    {
        $this->lignes_de_commande = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|LigneDeCommande[]
     */
    public function getLignesDeCommande(): Collection
    {
        return $this->lignes_de_commande;
    }

    public function addLigneDeCommande(LigneDeCommande $ligneDeCommande): self
    {
        if (!$this->lignes_de_commande->contains($ligneDeCommande)) {
            $this->lignes_de_commande[] = $ligneDeCommande;
            $ligneDeCommande->setCommande($this);
        }

        return $this;
    }

    public function removeLigneDeCommande(LigneDeCommande $ligneDeCommande): self
    {
        if ($this->lignes_de_commande->contains($ligneDeCommande)) {
            $this->lignes_de_commande->removeElement($ligneDeCommande);
            // set the owning side to null (unless already changed)
            if ($ligneDeCommande->getCommande() === $this) {
                $ligneDeCommande->setCommande(null);
            }
        }

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getModePaiement(): ?ModePaiement
    {
        return $this->mode_paiement;
    }

    public function setModePaiement(?ModePaiement $mode_paiement): self
    {
        $this->mode_paiement = $mode_paiement;

        return $this;
    }

    public function getPrixTotal()
    {
        $total = 0;
        foreach($this->lignes_de_commande as $ligne_de_commande){
            $total += $ligne_de_commande->getMontant();
        }
        return $total;
    }

    public function getNbArticles()
    {
        $total = 0;
        foreach($this->lignes_de_commande as $ligne_de_commande){
            $total += $ligne_de_commande->getQte();
        }
        return $total;
    }

    public function getStatutCommande(): ?StatutCommande
    {
        return $this->statut_commande;
    }

    public function setStatutCommande(?StatutCommande $statut_commande): self
    {
        $this->statut_commande = $statut_commande;

        return $this;
    }
}
