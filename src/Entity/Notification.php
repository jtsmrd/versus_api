<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     itemOperations={
 *          "get",
 *          "put"={
*               "security"="is_granted('IS_AUTHENTICATED_FULLY') and object.user == user",
 *              "denormalization_context"={
 *                  "groups"={"put"}
 *              },
 *              "normalization_context"={
 *                  "groups"={"user-notifications"}
 *              }
 *          }
 *      },
 *     subresourceOperations={
 *          "api_users_notifications_get_subresource"={
 *              "normalization_context"={
 *                  "groups"={"user-notifications"}
 *              }
 *          }
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 */
class Notification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user-notifications"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\NotificationType", inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"user-notifications"})
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user-notifications"})
     */
    private $createDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user-notifications"})
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=1000)
     * @Groups({"user-notifications"})
     */
    private $payload;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $pushDate;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user-notifications", "put"})
     */
    private $wasViewed;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $apnsToken;

    public function __construct()
    {
        $this->wasViewed = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?NotificationType
    {
        return $this->type;
    }

    public function setType(?NotificationType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getPayload(): ?string
    {
        return $this->payload;
    }

    public function setPayload(string $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    public function getPushDate(): ?\DateTimeInterface
    {
        return $this->pushDate;
    }

    public function setPushDate(?\DateTimeInterface $pushDate): self
    {
        $this->pushDate = $pushDate;

        return $this;
    }

    public function getWasViewed(): ?bool
    {
        return $this->wasViewed;
    }

    public function setWasViewed(bool $wasViewed): self
    {
        $this->wasViewed = $wasViewed;

        return $this;
    }

    public function getApnsToken(): ?string
    {
        return $this->apnsToken;
    }

    public function setApnsToken(?string $apnsToken): self
    {
        $this->apnsToken = $apnsToken;

        return $this;
    }
}
