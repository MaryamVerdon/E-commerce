<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategorieRepository")
 */
class Categorie
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
     * @ORM\OneToMany(targetEntity="App\Entity\TypeArticle", mappedBy="categorie")
     */
    private $types_articles;


    public function __construct()
    {
        $this->types_articles = new ArrayCollection();
        $this->articles = new ArrayCollection();
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

    /**
     * @return Collection|TypeArticle[]
     */
    public function getTypesArticles(): Collection
    {
        return $this->types_articles;
    }

    public function addTypesArticle(TypeArticle $typesArticle): self
    {
        if (!$this->types_articles->contains($typesArticle)) {
            $this->types_articles[] = $typesArticle;
            $typesArticle->setCategorie($this);
        }

        return $this;
    }

    public function removeTypesArticle(TypeArticle $typesArticle): self
    {
        if ($this->types_articles->contains($typesArticle)) {
            $this->types_articles->removeElement($typesArticle);
            // set the owning side to null (unless already changed)
            if ($typesArticle->getCategorie() === $this) {
                $typesArticle->setCategorie(null);
            }
        }

        return $this;
    }


}
