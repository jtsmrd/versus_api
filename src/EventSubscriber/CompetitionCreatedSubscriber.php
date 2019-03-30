<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-03-23
 * Time: 16:58
 */

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Competition;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CompetitionCreatedSubscriber implements EventSubscriberInterface
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
            KernelEvents::VIEW => ['competitionCreated', EventPriorities::PRE_WRITE]
        ];
    }

    public function competitionCreated(GetResponseForControllerResultEvent $event)
    {
        $competition = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$competition instanceof Competition || Request::METHOD_POST !== $method) {
            return;
        }

        $startDate = new \DateTime();

        // Now + 1 day
        $expireDate = $startDate->add(new DateInterval('P1D'));

        // Get each Entry record
        $leftEntry = $competition->getLeftEntry();
        $rightEntry = $competition->getRightEntry();

        // TODO: Validate left and right Entry types match
        $categoryId = $leftEntry->getCategoryId();
        $typeId = $leftEntry->getTypeId();

        $leftEntryFeatured = $leftEntry->getFeatured();
        $rightEntryFeatured = $rightEntry->getFeatured();

        $featured = false;

        if ($leftEntryFeatured || $rightEntryFeatured) {
            $featured = true;
        }

        $competition->setStartDate($startDate);
        $competition->setExpireDate($expireDate);
        $competition->setFeatured($featured);
        $competition->setCategoryId($categoryId);
        $competition->setTypeId($typeId);

        // Get User records
        $leftEntryUser = $leftEntry->getUser();
        $rightEntryUser = $rightEntry->getUser();

        $competition->addUser($leftEntryUser);
        $competition->addUser($rightEntryUser);

        // Set matchDate and matched flag
        $leftEntry->setMatchDate($startDate);
        $leftEntry->setMatched(true);
        $this->entityManager->persist($leftEntry);

        // Set matchDate and matched flag
        $rightEntry->setMatchDate($startDate);
        $rightEntry->setMatched(true);
        $this->entityManager->persist($rightEntry);

        $this->entityManager->flush();
    }
}