<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ApiResource(
 *     itemOperations={
 *          "get",
 *          "post",
 *          "put",
 *          "delete"
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\VoteRepository")
 * @UniqueEntity(fields={"user", "competition"})
// * @UniqueEntity("competition")
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

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Competition", inversedBy="votes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $competition;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntry(): ?Entry
    {
        return $this->entry;
    }

    /**
     * @ParamConverter("entry", class="App:Entry")
     * @param Entry|null $entry
     * @return Vote
     */
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

    public function getCompetition(): ?Competition
    {
        return $this->competition;
    }

    public function setCompetition(?Competition $competition): self
    {
        $this->competition = $competition;

        return $this;
    }
}
