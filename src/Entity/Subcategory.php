<?php

namespace App\Entity;

use App\Traits\Timestamp;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SubcategoryRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Traits\Slugger;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: SubcategoryRepository::class),
    ORM\HasLifecycleCallbacks()
]
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['groups' => ['read:subcategories']]],
        'post' => ['denormalization_context' => ['groups' => ['create:subcategories']]]
    ],
    itemOperations: [
        'get' => ['normalization_context' => ['groups' => ['read:subcategories', 'read:timestamp', 'read:slug']]],
        'patch' => ['denormalization_context' => ['groups' => ['update:subcategory']]],
        'delete'
    ]

)]
class Subcategory
{
    use Timestamp, Slugger;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:subcategories'])]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['create:subcategories', 'read:subcategories', 'update:subcategory', 'read:categories'])]
    #[
        Assert\Length(
            min: 5,
            minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
            max: 100,
            maxMessage: "Le nom ne doit dépasser {{ limit }} caractères"
        ),
        Assert\NotBlank(message: "Le nom est obligatoire")
    ]
    private $name;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'subcategories')]
    #[Groups(['create:subcategories', 'read:subcategories', 'update:subcategory'])]
    #[Assert\NotBlank(message: "Veuillez associer une catégorie")]
    private $category;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class)]
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }
}
