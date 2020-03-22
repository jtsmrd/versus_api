<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-03-17
 * Time: 18:35
 */

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Follower;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class FollowerCreatedSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['followerCreated', EventPriorities::POST_WRITE]
        ];
    }

    public function followerCreated(GetResponseForControllerResultEvent $event)
    {
        $followerRecord = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$followerRecord instanceof Follower || Request::METHOD_POST !== $method) {
            return;
        }

        $followedUser = $followerRecord->getFollowedUser();
        $followedUserFollowerCount = $followedUser->getFollowerCount();
        $followedUser->setFollowerCount($followedUserFollowerCount + 1);

        $this->entityManager->persist($followedUser);

        $follower = $followerRecord->getFollower();
        $followerFollowedUserCount = $follower->getFollowedUserCount();
        $follower->setFollowedUserCount($followerFollowedUserCount + 1);

        $this->entityManager->persist($follower);
        $this->entityManager->flush();
    }
}