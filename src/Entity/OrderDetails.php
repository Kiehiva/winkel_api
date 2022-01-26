<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\OrderDetailsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderDetailsRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['groups' => ['read:details']]],
        'post' => ['denormalization_context' => ['groups' => ['create:details']]]
    ],
    itemOperations: [
        'get' => ['normalization_context' => ['groups' => ['read:details']]]
    ]
)]
class OrderDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:orders'])]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:details', 'create:details'])]
    #[Assert\NotBlank(message: "Indiquez le nom du produit")]
    private $productName;

    #[ORM\Column(type: 'float')]
    #[Groups(['read:details', 'create:details'])]
    #[Assert\NotBlank(message: "Indiquez le prix du produit")]
    private $productPrice;

    #[ORM\Column(type: 'integer')]
    #[Groups(['read:details', 'create:details'])]
    #[Assert\NotBlank(message: "Combien en voulez vous?")]
    private $quantity;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(message: "Quel est sa TVA?")]
    #[Groups(['read:details', 'create:details'])]
    private $tva;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderDetails')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:details', 'create:details'])]
    #[Assert\NotBlank(message: "Associez le à une commande")]
    private $myOrder;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): self
    {
        $this->productName = $productName;

        return $this;
    }

    public function getProductPrice(): ?float
    {
        return $this->productPrice;
    }

    public function setProductPrice(float $productPrice): self
    {
        $this->productPrice = $productPrice;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(float $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function getMyOrder(): ?Order
    {
        return $this->myOrder;
    }

    public function setMyOrder(?Order $myOrder): self
    {
        $this->myOrder = $myOrder;

        return $this;
    }
}
