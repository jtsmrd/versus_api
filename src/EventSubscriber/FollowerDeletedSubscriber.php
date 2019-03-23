<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-03-22
 * Time: 19:02
 */

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Follower;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class FollowerDeletedSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['followerDeleted', EventPriorities::PRE_WRITE]
        ];
    }

    public function followerDeleted(GetResponseForControllerResultEvent $event)
    {
        $followerRecord = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$followerRecord instanceof Follower || Request::METHOD_DELETE !== $method) {
            return;
        }

        $followedUser = $followerRecord->getFollowedUser();
        $followedUserFollowerCount = $followedUser->getFollowerCount();
        $followedUser->setFollowerCount($followedUserFollowerCount - 1);

        $this->entityManager->persist($followedUser);

        $follower = $followerRecord->getFollower();
        $followerFollowedUserCount = $follower->getFollowedUserCount();
        $follower->setFollowedUserCount($followerFollowedUserCount - 1);

        $this->entityManager->persist($follower);
        $this->entityManager->flush();
    }
}