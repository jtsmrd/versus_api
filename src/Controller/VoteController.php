<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2020-01-05
 * Time: 16:43
 */

namespace App\Controller;


use App\Entity\Vote;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class VoteController extends AbstractController
{
    /**
     * @Route("/api/get-user-vote/{competitionId}")
     */
    public function getUserVoteForCompetition($competitionId)
    {
        $repository = $this->getDoctrine()->getRepository(Vote::class);
        $vote = $repository->getUserVoteForCompetition($competitionId, $this->getUser());
        return $this->json($vote);
    }
}