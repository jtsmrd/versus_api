<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={"order"={"createDate": "DESC"}},
 *     itemOperations={
 *          "get",
 *          "put"={
 *              "access_control"="is_granted('ROLE_USER') and object.getUser() == user"
 *          }
 *      },
 *     collectionOperations={
 *          "get",
 *          "post"={
 *              "access_control"="is_granted('ROLE_USER')"
 *          },
 *          "api_users_entries_get_subresource"={
 *              "normalization_context"={
 *                  "groups"={"get"}
 *              }
 *          }
 *      },
 *     denormalizationContext={
 *          "groups"={"post"}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\EntryRepository")
 */
class Entry implements UserCreatedEntityInterface, CreateDateEntityInterface, UpdateDateEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get", "get-owner"})
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"post", "get", "get-owner"})
     * @Assert\Length(max="100")
     */
    private $caption;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"post", "get", "get-owner"})
     * @Assert\NotBlank()
     */
    private $categoryId;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"post", "get", "get-owner"})
     * @Assert\NotBlank()
     */
    private $typeId;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get", "get-owner"})
     */
    private $createDate;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"post", "get", "get-owner"})
     */
    private $featured;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $matchDate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post", "get", "get-owner"})
     * @Assert\NotBlank()
     */
    private $mediaId;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"post", "get", "get-owner"})
     */
    private $rankId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $updateDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="entries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(?string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getTypeId(): ?int
    {
        return $this->typeId;
    }

    public function setTypeId(int $typeId): self
    {
        $this->typeId = $typeId;

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

    public function getFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    public function getMatchDate(): ?\DateTimeInterface
    {
        return $this->matchDate;
    }

    public function setMatchDate(?\DateTimeInterface $matchDate): self
    {
        $this->matchDate = $matchDate;

        return $this;
    }

    public function getMediaId(): ?string
    {
        return $this->mediaId;
    }

    public function setMediaId(string $mediaId): self
    {
        $this->mediaId = $mediaId;

        return $this;
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

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return Entry
     */
    public function setUser(UserInterface $user): UserCreatedEntityInterface
    {
        $this->user = $user;

        return $this;
    }

    public function __toString()
    {
        return "";
    }
}
