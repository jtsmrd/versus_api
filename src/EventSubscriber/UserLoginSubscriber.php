<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-03-10
 * Time: 14:11
 */

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Serializer;

class UserLoginSubscriber extends AbstractController
{
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $user = $event->getUser();
        $data = $event->getData();

        if (!$user instanceof UserInterface) {
            return;
        }

        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');

        $userEntity = $serializer->normalize($user, 'json', []);

        $data['user'] = $userEntity;

        $event->setData($data);
    }
}