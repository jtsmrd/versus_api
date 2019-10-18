<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-04-07
 * Time: 16:43
 */

namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class VoteCreatedSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['voteCreated', EventPriorities::PRE_WRITE]
        ];
    }

    public function voteCreated(GetResponseForControllerResultEvent $event)
    {
        $vote = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$vote instanceof Vote || Request::METHOD_POST !== $method) {
            return;
        }

        $entry = $vote->getEntry();
        $entry->setVoteCount($entry->getVoteCount() + 1);

        $this->entityManager->persist($entry);
        $this->entityManager->flush();
    }
}