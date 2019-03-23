<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-02-02
 * Time: 14:06
 */

namespace App\Controller;

use App\Entity\Follower;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{



    /**
     * @Route("/api/users/username/{username}", name="user_get_with_username", requirements={"id"="\w+"})
     * @ParamConverter("user", class="App:User")
     * @param $user
     * @return JsonResponse
     */
    public function user($user)
    {
        return $this->json($user);
    }

    /**
     * @Route("/api/users/{userId}/followed_user_ids", name="get_followed_user_ids")
     * @param integer $userId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getFollowedUserIds($userId)
    {
        $repository = $this->getDoctrine()->getRepository(Follower::class);
        $followerRecords = $repository->findBy(['follower' => $userId]);

        $followedUserIds = array_map(
            function (Follower $item) {
                return $item->getFollowedUser()->getId();
            },
            $followerRecords
        );

        return $this->json(
            ["followedUserIds" => $followedUserIds]
        );
    }


//    /**
//     * @Route("/add", name="user_add", methods={"POST"})
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\JsonResponse
//     */
//    public function add(Request $request)
//    {
//        /** @var Serializer $serializer */
//        $serializer = $this->get('serializer');
//
//        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
//
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($user);
//        $em->flush();
//
//        return $this->json($user);
//    }


//    /**
//     * @Route("/user/{id}", name="get_user_by_id", requirements={"id"="\d+"})
//     * @ParamConverter("user", class="App:User")
//     * @param $user
//     * @return \Symfony\Component\HttpFoundation\JsonResponse
//     */
//    public function user($user)
//    {
//        return $this->json($user);
//    }


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