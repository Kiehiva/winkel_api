<?php

namespace App\Entity;

use App\Traits\Timestamp;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: CategoryRepository::class),
    ORM\HasLifecycleCallbacks()
]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['read:categories']]
        ],
        'post' => [
            'denormalization_context' => ['groups' => 'create:categories']
        ]
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['read:categories', 'read:default']]
        ],
        'patch' => [
            'denormalization_context' => ['groups' => 'update:category']
        ],
        'delete'
    ]
)]
class Category
{
    use Timestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:categories'])]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:categories', 'create:categories', 'update:category', 'read:subcategories'])]
    #[
        Assert\Length(
            min: 5,
            minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
            max: 100,
            maxMessage: "Le nom ne peut contenir plus de {{ limit }} caractères."
        ),
        Assert\NotBlank(message: "Le nom est obligatoire")
    ]
    private $name;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Subcategory::class)]
    #[Groups(['read:categories'])]
    private $subcategories;

    public function __construct()
    {
        $this->subcategories = new ArrayCollection();
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

    /**
     * @return Collection|Subcategory[]
     */
    public function getSubcategories(): Collection
    {
        return $this->subcategories;
    }

    public function addSubcategory(Subcategory $subcategory): self
    {
        if (!$this->subcategories->contains($subcategory)) {
            $this->subcategories[] = $subcategory;
            $subcategory->setCategory($this);
        }

        return $this;
    }

    public function removeSubcategory(Subcategory $subcategory): self
    {
        if ($this->subcategories->removeElement($subcategory)) {
            // set the owning side to null (unless already changed)
            if ($subcategory->getCategory() === $this) {
                $subcategory->setCategory(null);
            }
        }

        return $this;
    }
}
