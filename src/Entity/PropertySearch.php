<?php
// src/Model/PropertySearch.php
// src/Model/PropertySearch.php
namespace App\Entity;

use App\Entity\Category;

class PropertySearch
{
    private ?string $nom = null;
    private ?Category $category = null;

    // For 'nom' property
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    // For 'category' property (ADD THESE)
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }
}