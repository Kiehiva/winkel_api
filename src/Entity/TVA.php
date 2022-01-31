<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TVARepository;
use App\Traits\Timestamp;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TVARepository::class)]
#[ORM\HasLifecycleCallbacks()]
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['groups' => ['read:tvas']]],
        'post' => ['denormalization_context' => ['groups' => ['create:tvas']]]
    ],
    itemOperations: [
        'get' => ['normalization_context' => ['groups' => ['read:tvas']]],
        'patch' => ['denormalization_context' => ['groups' => ['update:tva']]],
        'delete'
    ]
)]
class TVA
{
    use Timestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:tvas', 'create:tvas', 'update:tva', 'read:products'])]
    private $name;

    #[ORM\Column(type: 'float')]
    #[Groups(['read:tvas', 'create:tvas', 'update:tva', 'read:products'])]
    private $value;

    #[ORM\OneToMany(mappedBy: 'TVA', targetEntity: Product::class)]
    private $product;

    public function __construct()
    {
        $this->product = new ArrayCollection();
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

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->product->contains($product)) {
            $this->product[] = $product;
            $product->setTVA($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->product->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getTVA() === $this) {
                $product->setTVA(null);
            }
        }

        return $this;
    }
}
