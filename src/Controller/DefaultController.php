<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-02-02
 * Time: 13:46
 */

namespace App\Controller;

use App\Entity\User;
use App\Security\UserConfirmationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default_index")
     */
    public function index()
    {
        return $this->render(
            'base.html.twig'
        );
    }

    /**
     * @Route("/confirm-user/{token}", name="default_confirm_token")
     * @param string $token
     * @param UserConfirmationService $userConfirmationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \App\Exception\InvalidConfirmationTokenException
     */
    public function confirmUser(
        string $token,
        UserConfirmationService $userConfirmationService
    ) {
        $userConfirmationService->confirmUser($token);

        return $this->redirectToRoute('default_index');
    }

    /**
     * @Route("/username_available/{username}", name="username_available")
     * @param string $username
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function usernameExists($username)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(['username' => $username]);
        $exists = $user === null;
        return $this->json(["available" => $exists], Response::HTTP_OK);
    }
}