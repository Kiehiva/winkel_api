<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\{Slugger, Timestamp};
use App\Repository\ProductRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: ProductRepository::class),
    ORM\HasLifecycleCallbacks()
]
#[ApiResource(
    collectionOperations: [
        'post' => ['denormalization_context' => ['groups' => ['create:products']]],
        'get' => ['normalization_context' => ['groups' => ['read:products']]]
    ],
    itemOperations: [
        'patch' => ['denormalization_context' => ['groups' => ['update:product']]],
        'get' => ['normalization_context' => ['groups' => ['read:products', 'read:timestamp', 'read:slug']]],
        'delete'
    ]
)]
class Product
{
    use Timestamp, Slugger;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:products', 'create:products', 'update:product'])]
    #[
        Assert\NotBlank(message: "Veuillez renseigner un nom"),
        Assert\Length(
            min: 10,
            max: 100,
            minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
            maxMessage: "Le nom doit contenir au plus {{ limit }} caractères"
        )
    ]
    private $name;

    #[ORM\Column(type: 'float')]
    #[Groups(['read:products', 'create:products', 'update:product'])]
    #[
        Assert\NotBlank(message: "Veuillez renseigner un prix"),
        Assert\PositiveOrZero(message: "Le prix ne peut négatif")
    ]
    private $price;

    #[ORM\Column(type: 'text')]
    #[Groups(['read:products', 'create:products', 'update:product'])]
    #[
        Assert\NotBlank(message: "La description ne peut être vide"),
        Assert\Length(
            min: 10,
            minMessage: "La description doit contenir au moins {{ limit }} caractères"
        )
    ]
    private $description;

    #[ORM\Column(type: 'integer')]
    #[Groups(['read:products', 'create:products', 'update:product'])]
    #[
        Assert\NotBlank(message: "Veuillez indiquer la quantité restante"),
        Assert\PositiveOrZero(message: "La quantité doit être supérieure ou égale à zéro")
    ]
    private $quantityLeft;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['read:products', 'create:products', 'update:product'])]
    #[
        Assert\NotBlank(message: "Publions nous le produit?"),
        Assert\Type(
            type: "bool",
            message: "Cette valeur doit être de type {{ type }}"
        )
    ]
    private $isPublished;

    #[ORM\ManyToOne(targetEntity: Subcategory::class, inversedBy: 'products')]
    private $category;

    #[ORM\ManyToOne(targetEntity: TVA::class, inversedBy: 'product')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["read:products"])]
    private $TVA;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

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

    public function getQuantityLeft(): ?int
    {
        return $this->quantityLeft;
    }

    public function setQuantityLeft(int $quantityLeft): self
    {
        $this->quantityLeft = $quantityLeft;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getCategory(): ?Subcategory
    {
        return $this->category;
    }

    public function setCategory(?Subcategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getTVA(): ?TVA
    {
        return $this->TVA;
    }

    public function setTVA(?TVA $TVA): self
    {
        $this->TVA = $TVA;

        return $this;
    }
}
