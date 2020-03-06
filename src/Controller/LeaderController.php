<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2020-02-15
 * Time: 14:44
 */

namespace App\Controller;


use App\Entity\Leader;
use App\Repository\LeaderRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LeaderController extends AbstractController
{
    /**
     * @var LeaderRepository
     */
    private $leaderRepository;

    public function __construct(LeaderRepository $leaderRepository)
    {
        $this->leaderRepository = $leaderRepository;
    }

    /**
     * @Route("/api/leaders/weekly", name="get_weekly_leaders")
     * @return Collection|Leader[]
     */
    public function getWeeklyLeaders()
    {
        $leaders = $this->leaderRepository->getWeeklyLeaders();

        return $this->json(
            $leaders,
            Response::HTTP_OK,
            [],
            ['groups' => ['get-leaders']]
        );
    }

    /**
     * @Route("/api/leaders/monthly", name="get_monthly_leaders")
     * @return Collection|Leader[]
     */
    public function getMonthlyLeaders()
    {
        $leaders = $this->leaderRepository->getMonthlyLeaders();

        return $this->json(
            $leaders,
            Response::HTTP_OK,
            [],
            ['groups' => ['get-leaders']]
        );
    }

    /**
     * @Route("/api/leaders/alltime", name="get_all_time_leaders")
     * @return Collection|Leader[]
     */
    public function getAllTimeLeaders()
    {
        $leaders = $this->leaderRepository->getAllTimeLeaders();

        return $this->json(
            $leaders,
            Response::HTTP_OK,
            [],
            ['groups' => ['get-leaders']]
        );
    }
}