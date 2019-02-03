<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-02-02
 * Time: 13:46
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        return new JsonResponse([
            'action' => 'index',
            'time' => time()
        ]);
    }
}