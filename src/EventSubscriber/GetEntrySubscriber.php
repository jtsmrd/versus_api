<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-12-27
 * Time: 17:06
 */

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Entry;
use App\Entity\Vote;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetEntrySubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['getEntry', EventPriorities::PRE_RESPOND]
        ];
    }

    public function getEntry(GetResponseForControllerResultEvent $event)
    {
        $entry = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$entry instanceof Entry || Request::METHOD_GET !== $method) {
            return;
        }

        $user = $this->tokenStorage->getToken()->getUser();
        $userIds = array_map(
            function(Vote $vote) {
                return $vote->getUser()->getId();
            },
            $entry->getVotes()
        );
//        $userVoted = $userIds

        $entry->setUserVoted(true);
    }
}