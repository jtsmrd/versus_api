<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\LeaderboardRepository")
 */
class Leaderboard
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $featureImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $backgroundImage;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\LeaderboardType", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="integer")
     */
    private $resultLimit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFeatureImage(): ?string
    {
        return $this->featureImage;
    }

    public function setFeatureImage(?string $featureImage): self
    {
        $this->featureImage = $featureImage;

        return $this;
    }

    public function getBackgroundImage(): ?string
    {
        return $this->backgroundImage;
    }

    public function setBackgroundImage(?string $backgroundImage): self
    {
        $this->backgroundImage = $backgroundImage;

        return $this;
    }

    public function getType(): ?LeaderboardType
    {
        return $this->type;
    }

    public function setType(LeaderboardType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getResultLimit(): ?int
    {
        return $this->resultLimit;
    }

    public function setResultLimit(int $resultLimit): self
    {
        $this->resultLimit = $resultLimit;

        return $this;
    }
}
