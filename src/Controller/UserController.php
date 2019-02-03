<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-02-02
 * Time: 14:06
 */

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/add", name="user_add", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function add(Request $request)
    {
        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');

        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json($user);
    }

    /**
     * @Route("/user/{id}", name="get_user_by_id", requirements={"id"="\d+"})
     * @ParamConverter("user", class="App:User")
     * @param $user
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function user($user)
    {
        return $this->json($user);
    }


    /**
     * @Route("/user/{username}", name="get_user_by_username")
     * @ParamConverter("user", class="App:User")
     * @param $user
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function userByUsername($user)
    {
        return $this->json($user);
    }


//    public function list($page = 1, Request $request)
//    {
//        $limit = $request->get('limit', 10);
//        $repository = $this->getDoctrine()->getRepository(User::class);
//        $items = $repository->findAll();
//
//        return $this->json(
//            [
//                'page' => $page,
//                'limit' => $limit,
//                'data' => array_map(function (User $item) {
//                    return $item;
//                })
//            ]
//        );
//    }
}