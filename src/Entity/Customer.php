<?php

namespace App\Entity;

use App\Traits\Timestamp;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CustomerRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UserUpdatePassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\HasLifecycleCallbacks()]
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['groups' => ['read:users']]],
        'post' => [
            'denormalization_context' => ['groups' => ['create:users']],
            'validation_groups' => ['create:users']
        ]
    ],
    itemOperations: [
        'get' => ['normalization_context' => ['groups' => ['read:users']]],
        'patch' => [
            'denormalization_context' => ['groups' => ['update:user']],
            'validation_groups' => ['update:user']
        ],
        'delete',
        'update_password' => [
            'method' => 'post',
            'validation_groups' => ['update:user:password'],
            'denormalization_context' => ['groups' => ['update:user:password']],
            'path' => 'customers/{id}/update-password',
            'controller' => UserUpdatePassword::class,
            'openapi_context' => [
                'summary' => 'Update the Customer password',
                'description' => 'Update the Customer password'
            ]
        ]
    ]
)]
#[UniqueEntity(
    fields: 'email',
    message: 'Cet email est déjà utilisé'
)]
class Customer implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Timestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:users'])]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(['read:users', 'create:users'])]
    #[Assert\NotBlank(message: "L'email est obligatoire", groups: ['create:users'])]
    #[Assert\Email(message: "L'eamil saisit n'est pas valide", groups: ['create:users'])]
    private $email;

    #[ORM\Column(type: 'json')]
    #[Groups(['create:users'])]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    #[Groups(['create:users'])]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire", groups: ['create:users'])]
    #[Assert\Regex(
        pattern: "/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/",
        message: "Le mot de passe doit contenir au moins 8 caractères, 1 majuscule, 1 minuscule et un chiffre",
        groups: ['create:users']
    )]
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:users', 'create:users', 'update:user'])]
    #[Assert\NotBlank(message: "Le prénom est obligatoire", groups: ['create:users', 'update:user'])]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le prénom ne doit pas contenir plus de {{ limit }} caractères",
        groups: ['create:users', 'update:user']
    )]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:users', 'create:users', 'update:user'])]
    #[Assert\NotBlank(
        message: "Le nom de famille est obligatoire",
        groups: ['create:users', 'update:user']
    )]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne doit pas contenir plus de {{ limit }} caractères",
        groups: ['create:users', 'update:user']
    )]
    private $lastname;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['read:users', 'create:users', 'update:user'])]
    #[Assert\NotBlank(
        message: "Le numéro de téléphone est obligatoire",
        groups: ['create:users', 'update:user']
    )]
    #[Assert\Regex(
        pattern: "/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/",
        message: "Le numéro saisit est invalide",
        groups: ['create:users', 'update:user']
    )]
    private $phone;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['read:users', 'update:user'])]
    private $isVerified = false;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['read:users', 'update:user'])]
    private $isActivated = false;

    ########################### VIRTUAL FIELDS ###########################
    #[Groups(['update:user:password'])]
    #[SecurityAssert\UserPassword(
        message: "Le mot de passe saisit est incorrect",
        groups: ['update:user:password']
    )]
    private $oldPassword;

    #[Groups(['create:users', 'update:user:password'])]
    #[Assert\Expression(
        expression: "this.getPassword() === this.getVerifyPassword() or this.getNewPassword() === this.getVerifyPassword()",
        message: "Le mot de passe et la validation sont différents",
        groups: ['update:user:password']
    )]
    private $verifyPassword;

    #[Groups(['update:user:password'])]
    #[Assert\Regex(
        pattern: "/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/",
        message: "Le mot de passe doit contenir au moins 8 caractères, 1 majuscule, 1 minuscule et un chiffre",
        groups: ['update:user:password']
    )]
    #[Assert\NotBlank(
        message: "Veuillez saisir le nouveau mot de passe",
        groups: ['update:user:password']
    )]
    private $newPassword;

    ########################### END VIRTUAL FIELDS ###########################

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): null|string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getIsActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(bool $isActivated): self
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    public function getVerifyPassword()
    {
        return $this->verifyPassword;
    }

    public function setVerifyPassword($verifyPassword): self
    {
        $this->verifyPassword = $verifyPassword;

        return $this;
    }

    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    public function setOldPassword($oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword()
    {
        return $this->newPassword;
    }

    public function setNewPassword($newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }
}
