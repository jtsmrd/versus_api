<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-02-14
 * Time: 17:40
 */

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Entry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EntryCreatedSubscriber implements EventSubscriberInterface
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
            KernelEvents::VIEW => ['entryCreated', EventPriorities::PRE_WRITE]
        ];
    }

    public function entryCreated(GetResponseForControllerResultEvent $event)
    {
        $entry = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$entry instanceof Entry || Request::METHOD_POST !== $method) {
            return;
        }

        $user = $this->tokenStorage->getToken()->getUser();

        $entry->setRankId($user->getRankId());
        $entry->setFeatured($user->getFeatured());
    }
}