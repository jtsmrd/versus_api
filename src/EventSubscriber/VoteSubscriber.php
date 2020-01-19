<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2020-01-04
 * Time: 15:01
 */

namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Competition;
use App\Entity\Vote;
use App\Entity\Entry;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class VoteSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['voteAction', EventPriorities::PRE_WRITE]
        ];
    }

    public function voteAction(GetResponseForControllerResultEvent $event)
    {
        $vote = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($vote instanceof Vote && Request::METHOD_POST === $method)
        {
            $this->voteCreated($vote);
        }
        else if ($vote instanceof Vote && Request::METHOD_PUT === $method)
        {
            $this->voteUpdated($vote);
        }
        else if ($vote instanceof Vote && Request::METHOD_DELETE === $method)
        {
            $this->voteWillBeDeleted($vote);
        }
        else {
            return;
        }
    }

    public function voteCreated(Vote $vote)
    {
        $this->entityManager->persist($vote);
        $this->entityManager->flush();

        $entry = $vote->getEntry();
        $this->updateVoteCountForEntry($entry);
    }

    public function voteUpdated(Vote $vote)
    {
        $this->entityManager->persist($vote);
        $this->entityManager->flush();

        $entry = $vote->getEntry();

        $competition = $this->entityManager->getRepository(Competition::class)->find($vote->getCompetitionId());
        $previousVotedEntry = $competition->getEntryThatIsNotEntry($entry);

        $this->updateVoteCountForEntry($entry);
        $this->updateVoteCountForEntry($previousVotedEntry);
    }

    public function voteWillBeDeleted(Vote $vote)
    {
        $entry = $vote->getEntry();

        $this->entityManager->remove($vote);
        $this->entityManager->flush();

        $this->updateVoteCountForEntry($entry);
    }

    /**
     * @param Entry $entry
     */
    public function updateVoteCountForEntry(Entry $entry)
    {
        $entry->setVoteCount($entry->getVotes()->count());
        $this->entityManager->persist($entry);
        $this->entityManager->flush();
    }
}