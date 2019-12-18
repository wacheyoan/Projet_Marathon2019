<?php

namespace App\Controller;

use App\Entity\Comments;
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

        /** @var Comments $serieWithMostComments */
        $serieWithMostComments = $commentsRepository->findWithMostComments()->getSeries();
        $seriesRandom = $seriesRepository->findRandoms();
        $serieMostLike = $seriesRepository->findMostLiked();



        return $this->render('home/index.html.twig',[
            'serieWithMostComments' => $serieWithMostComments,
            'seriesRandom' => $seriesRandom,
            'serieMostLike' => $serieMostLike,
        ]);
    }
}
