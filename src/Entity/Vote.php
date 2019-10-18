<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\VoteRepository")
 */
class Vote implements UserCreatedEntityInterface, CreateDateEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $competitionId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Entry", inversedBy="votes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $entry;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="votes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompetitionId(): ?int
    {
        return $this->competitionId;
    }

    public function setCompetitionId(int $competitionId): self
    {
        $this->competitionId = $competitionId;

        return $this;
    }

    public function getEntry(): ?Entry
    {
        return $this->entry;
    }

    public function setEntry(?Entry $entry): self
    {
        $this->entry = $entry;

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

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(\DateTimeInterface $createDate): CreateDateEntityInterface
    {
        $this->createDate = $createDate;

        return $this;
    }
}
