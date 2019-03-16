<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\ResetPasswordAction;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "name": "partial",
 *          "username": "partial"
 *     }
 * )
 * @ApiResource(
 *     attributes={"order"={"name": "ASC"}},
 *     itemOperations={
 *          "get"={
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *              "normalization_context"={
 *                  "groups"={"get"}
 *              }
 *          },
 *          "put"={
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *              "denormalization_context"={
 *                  "groups"={"put"}
 *              },
 *              "normalization_context"={
 *                  "groups"={"get"}
 *              }
 *          },
 *          "put-reset-password"={
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *              "method"="PUT",
 *              "path"="/users/{id}/reset-password",
 *              "controller"=ResetPasswordAction::class,
 *              "denormalization_context"={
 *                  "groups"={"put-reset-password"}
 *              },
 *              "validation_groups"={"put-reset-password"}
 *          }
 *      },
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={
 *                  "groups"={"get"}
 *              }
 *          },
 *          "post"={
 *              "denormalization_context"={
 *                  "groups"={"post"}
 *              },
 *              "normalization_context"={
 *                  "groups"={"get"}
 *              },
 *              "validation_groups"={"post"}
 *          }
 *      }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User implements UserInterface, CreateDateEntityInterface, UpdateDateEntityInterface
{
    const DEFAULT_ENABLED = false;
    const DEFAULT_FEATURED = false;
    const DEFAULT_RANK_ID = 1;

    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';
    const ROLE_USER = 'ROLE_USER';

    const DEFAULT_ROLES = [self::ROLE_USER];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get", "get-owner"})
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"get", "post", "put", "get-owner"})
     */
    private $bio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get-admin"})
     */
    private $confirmationToken;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get-admin", "get-owner"})
     */
    private $createDate;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"get", "post", "put", "get-owner"})
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Length(min=6, max=50, groups={"post", "put"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"post", "put", "get-admin", "get-owner"})
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Email(groups={"post", "put"})
     * @Assert\Length(min=6, max=50, groups={"post", "put"})
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"get-admin", "get-owner"})
     */
    private $enabled;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"get", "get-owner"})
     */
    private $featured;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post", "get-admin"})
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="Password must be seven characters long and contain at least one number, one upper case letter, and one lower case letter",
     *     groups={"post"}
     * )
     */
    private $password;

    /**
     * @Groups({"post"})
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Expression(
     *     "this.getPassword() === this.getRetypedPassword()",
     *     message="Passwords do not match",
     *     groups={"post"}
     * )
     */
    private $retypedPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="Password must be seven characters long and contain at least one number, one upper case letter, and one lower case letter",
     *     groups={"put-reset-password"}
     * )
     */
    private $newPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @Assert\Expression(
     *     "this.getNewPassword() === this.getNewRetypedPassword()",
     *     message="Passwords do not match",
     *     groups={"put-reset-password"}
     * )
     */
    private $newRetypedPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @UserPassword(groups={"put-reset-password"})
     */
    private $oldPassword;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $passwordChangeDate;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"get", "get-owner"})
     */
    private $rankId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"get"})
     * @Assert\DateTime()
     */
    private $updateDate;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"get", "post", "get-owner"})
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Length(min=6, max=50, groups={"post"})
     */
    private $username;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Entry", mappedBy="user")
     * @Groups({"get-owner"})
     * @ApiSubresource()
     */
    private $entries;

    /**
     * @ORM\Column(type="simple_array", length=200)
     * @Groups({"get-admin"})
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     * @Groups({"get", "put", "get-owner"})
     */
    private $profileImage;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     * @Groups({"get", "put", "get-owner"})
     */
    private $backgroundImage;


    public function __construct()
    {
        $this->entries = new ArrayCollection();
        $this->roles = self::DEFAULT_ROLES;
        $this->enabled = self::DEFAULT_ENABLED;
        $this->featured = self::DEFAULT_FEATURED;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(\DateTimeInterface $createDate): CreateDateEntityInterface
    {
        $this->createDate = $createDate;

        return $this;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getFeatured(): bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }

    public function getRetypedPassword()
    {
        return $this->retypedPassword;
    }

    public function setRetypedPassword($retypedPassword): void
    {
        $this->retypedPassword = $retypedPassword;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword($newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getNewRetypedPassword(): ?string
    {
        return $this->newRetypedPassword;
    }

    public function setNewRetypedPassword($newRetypedPassword): void
    {
        $this->newRetypedPassword = $newRetypedPassword;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    public function getPasswordChangeDate()
    {
        return $this->passwordChangeDate;
    }

    public function setPasswordChangeDate($passwordChangeDate): void
    {
        $this->passwordChangeDate = $passwordChangeDate;
    }

    public function getRankId(): ?int
    {
        return $this->rankId;
    }

    public function setRankId(int $rankId): self
    {
        $this->rankId = $rankId;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(\DateTimeInterface $updateDate): CreateDateEntityInterface
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getProfileImage()
    {
        return $this->profileImage;
    }

    /**
     * @param mixed $profileImage
     */
    public function setProfileImage($profileImage): void
    {
        $this->profileImage = $profileImage;
    }

    /**
     * @return mixed
     */
    public function getBackgroundImage()
    {
        return $this->backgroundImage;
    }

    /**
     * @param mixed $backgroundImage
     */
    public function setBackgroundImage($backgroundImage): void
    {
        $this->backgroundImage = $backgroundImage;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {

    }
}
