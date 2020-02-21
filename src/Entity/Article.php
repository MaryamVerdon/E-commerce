<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $prix_u;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeArticle", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type_article;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Section", inversedBy="articles")
     */
    private $sections;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\QuantiteTaille", mappedBy="article", cascade={"persist"})
     */
    private $quantite_tailles;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->lignes_de_commande = new ArrayCollection();
        $this->quantite_tailles = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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

    public function getPrixU(): ?float
    {
        return $this->prix_u;
    }

    public function setPrixU(float $prix_u): self
    {
        $this->prix_u = $prix_u;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getTypeArticle(): ?TypeArticle
    {
        return $this->type_article;
    }

    public function setTypeArticle(?TypeArticle $type_article): self
    {
        $this->type_article = $type_article;

        return $this;
    }

    /**
     * @return Collection|Section[]
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): self
    {
        if (!$this->sections->contains($section)) {
            $this->sections[] = $section;
        }

        return $this;
    }

    public function removeSection(Section $section): self
    {
        if ($this->sections->contains($section)) {
            $this->sections->removeElement($section);
        }

        return $this;
    }

    /**
     * @return Collection|QuantiteTaille[]
     */
    public function getQuantiteTailles(): Collection
    {
        return $this->quantite_tailles;
    }

    public function addQuantiteTaille(QuantiteTaille $quantite_tailles): self
    {
        if (!$this->quantite_tailles->contains($quantite_tailles)) {
            $this->quantite_tailles[] = $quantite_tailles;
            $quantite_tailles->setArticle($this);
        }

        return $this;
    }

    public function removeQuantiteTaille(QuantiteTaille $quantite_tailles): self
    {
        if ($this->quantite_tailles->contains($quantite_tailles)) {
            $this->quantite_tailles->removeElement($quantite_tailles);
            // set the owning side to null (unless already changed)
            if ($quantite_tailles->getArticle() === $this) {
                $quantite_tailles->setArticle(null);
            }
        }

        return $this;
    }

}
