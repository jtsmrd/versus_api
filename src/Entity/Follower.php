<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "followedUser.id": "exact"
 *     }
 * )
 * @ApiResource(
 *     itemOperations={"get", "put", "delete"},
 *     collectionOperations={
 *          "get",
 *          "post"
 *     },
 *     subresourceOperations={
 *          "api_users_followed_users_get_subresource"={
 *              "normalization_context"={
 *                  "groups"={"followed-users"}
 *              }
 *          },
 *          "api_users_followers_get_subresource"={
 *              "normalization_context"={
 *                  "groups"={"followers"}
 *              }
 *          }
 *     },
 *     denormalizationContext={
 *          "groups"={"post"}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\FollowerRepository")
 * @ORM\EntityListeners({"App\Listener\EntityListener"})
 */
class Follower implements UserCreatedEntityInterface, CreateDateEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"followers", "followed-users"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"followers", "followed-users"})
     */
    private $createDate;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"followers", "followed-users"})
     */
    private $inviteAccepted;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="followedUsers")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"followers", "followed-users"})
     */
    private $follower;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="followers")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post", "followers", "followed-users"})
     */
    private $followedUser;

    public function __construct()
    {
        $this->inviteAccepted = true;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getInviteAccepted(): ?bool
    {
        return $this->inviteAccepted;
    }

    public function setInviteAccepted(bool $inviteAccepted): self
    {
        $this->inviteAccepted = $inviteAccepted;

        return $this;
    }

    /**
     * @return User
     */
    public function getFollower(): User
    {
        return $this->follower;
    }

    /**
     * @param UserInterface $user
     * @return Entry
     */
    public function setUser(UserInterface $user): UserCreatedEntityInterface
    {
        $this->follower = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFollowedUser(): User
    {
        return $this->followedUser;
    }

    /**
     * @param UserInterface $followedUser
     * @return Follower
     */
    public function setFollowedUser(UserInterface $followedUser)
    {
        $this->followedUser = $followedUser;

        return $this;
    }
}
