<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "featured": "exact",
 *          "categoryId": "exact"
 *     }
 * )
 * @ApiResource(
 *     itemOperations={"get", "delete"},
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={
 *                  "groups"={"get-user-competitions"}
 *              }
 *          },
 *          "post",
 *          "api_users_competitions_get_subresource"={
 *              "normalization_context"={
 *                  "groups"={"get-user-competitions"}
 *              }
 *          }
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\CompetitionRepository")
 */
class Competition
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get", "get-user-competitions"})
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"get", "get-user-competitions"})
     */
    private $active;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"get", "get-user-competitions"})
     */
    private $categoryId;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"get", "get-user-competitions"})
     */
    private $typeId;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get", "get-user-competitions"})
     */
    private $expireDate;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"get", "get-user-competitions"})
     */
    private $extended;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"get", "get-user-competitions"})
     */
    private $featured;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get", "get-user-competitions"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"get", "get-user-competitions"})
     */
    private $winnerUserId;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Entry")
     * @ApiSubresource()
     * @Groups({"get", "post", "get-user-competitions"})
     */
    private $leftEntry;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Entry")
     * @ApiSubresource()
     * @Groups({"get", "post", "get-user-competitions"})
     */
    private $rightEntry;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="competitions")
     */
    private $users;

    public function __construct()
    {
        $this->active = true;
        $this->extended = false;
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

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

    public function getExpireDate(): ?\DateTimeInterface
    {
        return $this->expireDate;
    }

    public function setExpireDate(\DateTimeInterface $expireDate): self
    {
        $this->expireDate = $expireDate;

        return $this;
    }

    public function getExtended(): ?bool
    {
        return $this->extended;
    }

    public function setExtended(bool $extended): self
    {
        $this->extended = $extended;

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

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getWinnerUserId(): ?int
    {
        return $this->winnerUserId;
    }

    public function setWinnerUserId(?int $winnerUserId): self
    {
        $this->winnerUserId = $winnerUserId;

        return $this;
    }

    /**
     * @return Entry
     */
    public function getLeftEntry(): Entry
    {
        return $this->leftEntry;
    }

    /**
     * @param mixed $leftEntry
     */
    public function setLeftEntry($leftEntry): void
    {
        $this->leftEntry = $leftEntry;
    }

    /**
     * @return Entry
     */
    public function getRightEntry(): Entry
    {
        return $this->rightEntry;
    }

    /**
     * @param mixed $rightEntry
     */
    public function setRightEntry($rightEntry): void
    {
        $this->rightEntry = $rightEntry;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addCompetition($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeCompetition($this);
        }

        return $this;
    }
}
