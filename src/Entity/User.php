<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\UserRepository;
use App\State\UserProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            normalizationContext: ['groups' => self::GROUP_GET],
            security: "is_granted('ROLE_ADMIN')",
            validationContext: ['groups' => self::GROUP_POST],
            processor: UserProcessor::class,
        ),
        new Put(
            normalizationContext: ['groups' => self::GROUP_GET],
            security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object == user)",
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
        ),
    ],
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private const GROUP_GET = 'user:get';
    private const GROUP_POST = 'user:post';

    #[Assert\NotBlank(groups: [self::GROUP_POST])]
    #[Assert\Length(min: 2)]
    #[ORM\Column]
    public ?string $firstName;

    #[Assert\NotBlank(groups: [self::GROUP_POST])]
    #[Assert\Length(min: 2)]
    #[ORM\Column]
    public ?string $lastName;

    #[Assert\NotBlank(groups: [self::GROUP_POST])]
    #[Assert\Regex('/^\+\d{7,}$/')]
    #[ORM\Column]
    public ?string $phone;

    #[Assert\NotBlank(groups: [self::GROUP_POST])]
    public ?string $plainPassword = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Email]
    #[Assert\NotBlank(groups: [self::GROUP_POST])]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    #[ApiProperty(security: "is_granted('ROLE_ADMIN')")]
    private ?array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials()
    {
    }
}
