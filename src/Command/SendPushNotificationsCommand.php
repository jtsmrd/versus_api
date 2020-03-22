<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2020-03-22
 * Time: 18:17
 */

namespace App\Command;


use App\Entity\Notification;
use App\PushNotification\PushNotificationService;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendPushNotificationsCommand extends Command
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        LoggerInterface $logger,
        NotificationRepository $notificationRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->logger = $logger;
        $this->notificationRepository = $notificationRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:sendPushNotifications')
            ->setDescription('Attempts to send push notifications.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $unsentNotificationsExist = true;

        while ($unsentNotificationsExist) {

            $notifications = $this->notificationRepository->getUnsentNotifications();

            if (!$notifications) {
                $unsentNotificationsExist = false;
                $this->logger->debug("No more unsent notifications");
                continue;
            }
            $this->logger->debug("Sending notifications");
            foreach ($notifications as $notification) {
                $this->pushNotification($notification);
            }
        }
    }

    private function pushNotification(Notification $notification)
    {
        $pushNotificationService = new PushNotificationService($this->logger);

        $token = $notification->getApnsToken();
        $message = $notification->getMessage();
        $payload = $notification->getPayload();

        $pushNotificationService->pushNotification(
            $token,
            $message,
            $payload
        );

        $notification->setPushDate(new \DateTime());
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
}