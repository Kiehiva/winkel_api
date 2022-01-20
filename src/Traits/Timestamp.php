<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait Timestamp
{

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['read:default'])]
    private $createAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['read:default'])]
    private $updatedAt;

    public function getCreateAt()
    {
        return $this->createAt;
    }

    public function setCreateAt($createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[
        ORM\PrePersist,
        ORM\PreUpdate
    ]
    public function updateDate(): void
    {
        if ($this->getCreateAt() === null)
            $this->setCreateAt(new \DateTimeImmutable());
        $this->setUpdatedAt(new \DateTimeImmutable());
    }
}
