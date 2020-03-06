<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-02-02
 * Time: 14:06
 */

namespace App\Controller;

use App\Entity\Competition;
use App\Entity\Follower;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
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

    /**
     * @Route("/api/users/{userId}/followed_user_competitions", name="get_followed_user_competitions", methods={"GET"})
     * @param $userId
     * @return mixed
     */
    public function getFollowedUserCompetitions(Request $request, $userId)
    {
        $page = $request->query->get('page', 0);
        $limit = $request->query->get('limit', 10);
        $offset = $page * $limit;

        $sql = 'select c.* from competition c
            join competition_user cu on c.id = cu.competition_id
            join follower f on cu.user_id = f.followed_user_id
            join user u on f.follower_id = u.id
            where u.id = ' . $userId . '
            order by c.start_date asc
            limit ' . $limit . '
            offset ' . $offset . '
        ';

//        and c.active = '1'

        $conn = $this->entityManager->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $competitionResults = $stmt->fetchAll();

        $compIds = array_map(
            function ($item) {
                return $item['id'];
            },
            $competitionResults
        );

        $compRepository = $this->getDoctrine()->getRepository(Competition::class);
        $compRecords = $compRepository->findBy(['id' => $compIds]);

        return $this->json(
            $compRecords,
            Response::HTTP_OK,
            [],
            ['groups' => ['get-user-competitions']]
        );



//        $repository = $this->getDoctrine()->getRepository(Follower::class);
//        $followerRecords = $repository->findBy(['follower' => $userId]);
//
//        $followedUserIds = array_map(
//            function (Follower $item) {
//                return $item->getFollowedUser()->getId();
//            },
//            $followerRecords
//        );
//
//        $sql = '
//          SELECT c.id
//          FROM competition c
//          JOIN competition_user cu on c.id = cu.competition_id
//          WHERE cu.user_id IN (' . implode( ", ", $followedUserIds) . ')
//          AND c.active = 1
//          ORDER BY c.start_date DESC
//        ';
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