<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SubcategoryRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubcategoryRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['groups' => ['read:subcategories']]],
        'post' => ['denormalization_context' => ['groups' => ['create:subcategories']]]
    ],
    itemOperations: [
        'get' => ['normalization_context' => ['groups' => ['read:subcategories']]],
        'patch' => ['denormalization_context' => ['groups' => ['update:subcategory']]],
        'delete'
    ]

)]
class Subcategory
{
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
}
