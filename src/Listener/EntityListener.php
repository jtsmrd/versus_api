<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2020-03-22
 * Time: 16:03
 */

namespace App\Listener;


use App\Entity\Competition;
use App\Entity\Entry;
use App\Entity\Follower;
use App\Entity\Notification;
use App\Entity\NotificationType;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Psr\Log\LoggerInterface;

class EntityListener
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @ORM\PostPersist()
     * @param $object
     * @param LifecycleEventArgs $args
     */
    public function postPersistHandler($object, LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->entityManager = $args->getEntityManager();

        if ($entity instanceof Competition) {
            $this->processCompetition($entity);
        }
        else if ($entity instanceof Follower) {
            $this->processFollower($entity);
        }
    }

    private function processCompetition(Competition $competition)
    {
        $this->createCompetitionMatchedNotification(
            $competition->getLeftEntry(),
            $competition->getId()
        );

        $this->createCompetitionMatchedNotification(
            $competition->getRightEntry(),
            $competition->getId()
        );
    }

    private function processFollower(Follower $follower)
    {
        $this->createFollowerNotification($follower);
    }

    private function createCompetitionMatchedNotification(
        Entry $entry,
        int $competitionId
    ) {
        $notificationTypeName = 'Competition Matched';
        $user = $entry->getUser();
        $message = 'Your entry was matched!';
        $payloadArray = [
            'competitionId' => $competitionId,
            'entryImage' => $entry->getMediaId()
        ];

        $this->createNotification(
            $notificationTypeName,
            $user,
            $message,
            $payloadArray
        );
    }

    private function createFollowerNotification(Follower $follower)
    {
        $followedUser = $follower->getFollowedUser();
        $followerUser = $follower->getFollower();

        $notificationTypeName = 'New Follower';
        $user = $followedUser;
        $message = '@' . $followerUser->getUsername() . ' started following you.';
        $payloadArray = [
            'followerUserId' => $followerUser->getId(),
            'followerUsername' => $followerUser->getUsername(),
            'followerProfileImage' => $followerUser->getProfileImage()
        ];

        $this->createNotification(
            $notificationTypeName,
            $user,
            $message,
            $payloadArray
        );
    }

    private function createNotification(
        string $notificationTypeName,
        User $user,
        string $message,
        array $payloadArray
    ) {
        $notificationTypeRepository = $this->entityManager->getRepository(NotificationType::class);
        $notificationType = $notificationTypeRepository->getNotificationType(
            $notificationTypeName
        );

        if (!$notificationType) {
            return null;
        }

        $notification = new Notification();
        $notification->setType($notificationType);
        $notification->setUser($user);
        $notification->setApnsToken($user->getApnsToken());
        $notification->setCreateDate(new \DateTime());
        $notification->setMessage($message);

        $payload = json_encode($payloadArray);
        $notification->setPayload($payload);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $this->logger->debug('Notification created');
    }
}