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
use App\Entity\Notification;
use App\Entity\NotificationType;
use App\Entity\User;
use App\PushNotification\PushNotificationService;
use App\Repository\NotificationTypeRepository;
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
     * @var NotificationTypeRepository
     */
    private $notificationTypeRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        NotificationTypeRepository $notificationTypeRepository,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->notificationTypeRepository = $notificationTypeRepository;
        $this->logger = $logger;
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

        $notification = $this->createFollowerNotification($followedUser, $follower);

        if ($notification && $notification->getApnsToken()) {
            $push = new PushNotificationService($this->logger);
            $push->pushNotification(
                $notification->getApnsToken(),
                $notification->getMessage(),
                $notification->getPayload()
            );
        }
    }

    private function createFollowerNotification(User $followedUser, User $follower): ?Notification
    {
        $notificationType = $this->notificationTypeRepository->getNotificationType(
            'New Follower'
        );

        if (!$notificationType) {
            return null;
        }

        $notification = new Notification();
        $notification->setType($notificationType);
        $notification->setUser($followedUser);
        $notification->setApnsToken($followedUser->getApnsToken());
        $notification->setCreateDate(new \DateTime());

        $message = '@' . $follower->getUsername() . ' started following you.';
        $notification->setMessage($message);

        $payloadArray = [
            'followerUserId' => $follower->getId(),
            'followerUsername' => $follower->getUsername(),
            'followerProfileImage' => $follower->getProfileImage()
        ];

        $payload = json_encode($payloadArray);
        $notification->setPayload($payload);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }
}