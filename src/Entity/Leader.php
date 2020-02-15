<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "leaderboardType": "start",
 *          "startDate": "start"
 *     }
 * )
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={
 *                  "groups"={"get-leaders"}
 *              }
 *          }
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\LeaderRepository")
 */
class Leader
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get-leaders"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-leaders"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LeaderboardType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $leaderboardType;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"get-leaders"})
     */
    private $winCount;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"get-leaders"})
     */
    private $voteCount;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get-leaders"})
     */
    private $startDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getLeaderboardType(): ?LeaderboardType
    {
        return $this->leaderboardType;
    }

    public function setLeaderboardType(?LeaderboardType $leaderboardType): self
    {
        $this->leaderboardType = $leaderboardType;

        return $this;
    }

    public function getWinCount(): ?int
    {
        return $this->winCount;
    }

    public function setWinCount(int $winCount): self
    {
        $this->winCount = $winCount;

        return $this;
    }

    public function getVoteCount(): ?int
    {
        return $this->voteCount;
    }

    public function setVoteCount(int $voteCount): self
    {
        $this->voteCount = $voteCount;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }
}
