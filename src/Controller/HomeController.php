<?php

namespace App\Controller;

use App\Repository\CommentsRepository;
use App\Repository\SeriesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CommentsRepository $commentsRepository,SeriesRepository $seriesRepository)
    {


        $serieWithMostComments = $commentsRepository->findWithMostComments();
        $seriesRandom = $seriesRepository->findRandoms();

        return $this->render('home/index.html.twig',[
            'serieWithMostComments' => $serieWithMostComments,
            'seriesRandom' => $seriesRandom,
        ]);
    }
}
