<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\OrderRepository;
use App\Traits\Timestamp;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ORM\HasLifecycleCallbacks()]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => 'read:orders']
        ],
        'post' => [
            'denormalization_context' => ['groups' => 'create:orders']
        ]
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['read:orders', 'read:default']]
        ],
        'patch' => [
            'denormalization_context' => ['groups' => ['update:order']]
        ]
        // Seul le status sera modifiable
        // Pas de possibilité de supprimer une commande. Endpoint DELETE non dispo
    ]
)]
class Order
{
    use Timestamp;

    const STATE = [
        0 => "En attente de paiement",
        1 => "Payé",
        2 => "En cours de production",
        3 => "Terminé et rendu"
    ];



    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:orders', 'read:details'])]
    private $reference;
    // La reference sera générée automatiquement lors de la création de la commande
    // Voir dans Events/CreateOrderSubscriber.php

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:orders', 'update:order'])]
    private $status;
    // Le statut sera générée automatiquement lors de la création de la commande
    // Voir dans Events/CreateOrderSubscriber.php

    #[ORM\OneToMany(mappedBy: 'myOrder', targetEntity: OrderDetails::class, orphanRemoval: true)]
    #[Groups(['read:orders'])]
    private $orderDetails;

    public function __construct()
    {
        $this->orderDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|OrderDetails[]
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetails $orderDetail): self
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails[] = $orderDetail;
            $orderDetail->setMyOrder($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetails $orderDetail): self
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getMyOrder() === $this) {
                $orderDetail->setMyOrder(null);
            }
        }

        return $this;
    }
}
