<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "matched": "exact"
 *     }
 * )
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
     * @Groups({"get", "get-owner", "get-user-competitions"})
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"post", "get", "get-owner", "get-user-competitions"})
     * @Assert\Length(max="100")
     */
    private $caption;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"post", "get", "get-owner", "get-user-competitions"})
     * @Assert\NotBlank()
     */
    private $categoryId;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"post", "get", "get-owner", "get-user-competitions"})
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
     * @Groups({"get", "get-owner"})
     * @Assert\DateTime()
     */
    private $matchDate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post", "get", "get-owner", "get-user-competitions"})
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
     * @Groups({"get-user-competitions"})
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"get", "get-owner"})
     */
    private $matched;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"get", "get-user-competitions"})
     */
    private $voteCount;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="entry")
     */
    private $votes;

    public function __construct()
    {
        $this->matched = false;
        $this->votes = new ArrayCollection();
    }

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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): UserCreatedEntityInterface
    {
        $this->user = $user;

        return $this;
    }

    public function __toString()
    {
        return "";
    }

    public function getMatched(): ?bool
    {
        return $this->matched;
    }

    public function setMatched(bool $matched): self
    {
        $this->matched = $matched;

        return $this;
    }

    public function getVoteCount()
    {
        return $this->voteCount;
    }

    public function setVoteCount($voteCount): void
    {
        $this->voteCount = $voteCount;
    }

    /**
     * @return Collection|Vote[]
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setEntry($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            // set the owning side to null (unless already changed)
            if ($vote->getEntry() === $this) {
                $vote->setEntry(null);
            }
        }

        return $this;
    }
}
