<?php
namespace App\Entity;

use App\Entity\Category;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "category_search")]
class CategorySearch
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;
    #[ORM\ManyToOne(targetEntity: Category::class)]
    private ?Category $category = null;

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
