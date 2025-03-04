<?php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[Vich\Uploadable]
#[UniqueEntity(fields: ['email'], message: 'This email is already used', groups: ['registration'])]
#[UniqueEntity(fields: ['username'], message: 'This username is already taken', groups: ['registration'])]
#[UniqueEntity(fields: ['cin'], message: 'This CIN is already used', groups: ['registration'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Le nom d\'utilisateur ne peut pas être vide', groups: ['registration'])]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Le nom d\'utilisateur doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le nom d\'utilisateur ne peut pas dépasser {{ limit }} caractères',
        groups: ['registration']
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*\d)[a-zA-Z0-9_-]+$/',
        message: 'Le nom d\'utilisateur doit contenir au moins un chiffre et ne peut contenir que des lettres, chiffres, tirets et underscores',
        groups: ['registration']
    )]
    private ?string $username = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'L\'email ne peut pas être vide', groups: ['registration'])]
    #[Assert\Email(message: 'L\'email {{ value }} n\'est pas un email valide', groups: ['registration'])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)] // Added missing annotation
    #[Assert\NotBlank(message: 'Le mot de passe ne peut pas être vide', groups: ['registration'])]
    #[Assert\Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères', groups: ['registration'])]
    #[Assert\Regex(
        pattern: '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/',
        message: 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial',
        groups: ['registration']
    )]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide', groups: ['registration'])]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères',
        groups: ['registration']
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le prénom ne peut pas être vide', groups: ['registration'])]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères',
        groups: ['registration']
    )]
    private ?string $surname = null;

    #[ORM\Column(length: 8, unique: true)]
    #[Assert\NotBlank(message: 'Le Cin ne peut pas être vide', groups: ['registration'])]
    #[Assert\Length(exactly: 8, exactMessage: 'Le CIN doit contenir exactement {{ limit }} chiffres', groups: ['registration'])]
    #[Assert\Regex(
        pattern: '/^[0-9]{8}$/', 
        message: 'Le CIN doit être composé uniquement de 8 chiffres',
        groups: ['registration']
    )]
    private ?string $cin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePicture = null;

    #[Vich\UploadableField(mapping: 'profile_pictures', fileNameProperty: 'profilePicture')]
    #[Assert\Image(
        maxSize: '2M',
        maxSizeMessage: 'L\'image ne doit pas dépasser 2MB',
        mimeTypes: ['image/jpeg', 'image/png'],
        mimeTypesMessage: 'Veuillez télécharger une image valide (JPG ou PNG)',
        groups: ['registration']
    )]
    private ?File $profilePictureFile = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetCode = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $resetCodeExpiresAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isProfileUpdated = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $googleId = null;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isMfaEnabled = false;
    
    public function isMfaEnabled(): bool { return $this->isMfaEnabled; }
    public function setIsMfaEnabled(bool $isMfaEnabled): self { $this->isMfaEnabled = $isMfaEnabled; return $this; }
    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
    }
    

    // Getters and Setters
    public function getId(): ?int { return $this->id; }

    public function getUsername(): ?string { return $this->username; }
    public function setUsername(string $username): self { $this->username = $username; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }
    public function setRoles(array $roles): self { $this->roles = $roles; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getSurname(): ?string { return $this->surname; }
    public function setSurname(string $surname): self { $this->surname = $surname; return $this; }

    public function getCin(): ?string { return $this->cin; }
    public function setCin(?string $cin): self { $this->cin = $cin; return $this; }

    public function getProfilePicture(): ?string { return $this->profilePicture; }
    public function setProfilePicture(?string $profilePicture): self { $this->profilePicture = $profilePicture; return $this; }

    public function getProfilePictureFile(): ?File { return $this->profilePictureFile; }
    public function setProfilePictureFile(?File $profilePictureFile = null): void
    {
        $this->profilePictureFile = $profilePictureFile;
        if (null !== $profilePictureFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    public function getResetCode(): ?string { return $this->resetCode; }
    public function setResetCode(?string $resetCode): self { $this->resetCode = $resetCode; return $this; }

    public function getResetCodeExpiresAt(): ?\DateTimeInterface { return $this->resetCodeExpiresAt; }
    public function setResetCodeExpiresAt(?\DateTimeInterface $resetCodeExpiresAt): self { $this->resetCodeExpiresAt = $resetCodeExpiresAt; return $this; }

    public function getIsProfileUpdated(): bool { return $this->isProfileUpdated; }
    public function setIsProfileUpdated(bool $isProfileUpdated): self { $this->isProfileUpdated = $isProfileUpdated; return $this; }

    public function getGoogleId(): ?string { return $this->googleId; }
    public function setGoogleId(?string $googleId): self { $this->googleId = $googleId; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    public function eraseCredentials(): void { /* Intentionally left empty */ }
}