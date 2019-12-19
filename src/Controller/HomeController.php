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
    public function index(CommentsRepository $commentsRepository,SeriesRepository $seriesRepository,
        UserRepository $userRepository)
    {

        /** @var Comments $serieWithMostComments */
        $serieWithMostComments = $commentsRepository->findWithMostComments();
        if($serieWithMostComments){
            $serieWithMostComments =$serieWithMostComments->getSeries();
        }
        $seriesRandom = $seriesRepository->findRandoms();
        $serieMostLike = $seriesRepository->findMostLiked();

        $users = $userRepository->findAll();

        $series= [];
        foreach ($users as $user){
           foreach ($user->getEpisodes() as $episode){
               if(!in_array($episode->getSeries(),$series)) {
                   $series[] = $episode->getSeries();
               }
               $series[array_search($episode->getSeries(),$series)]->episode[] = $episode;
           }
        }

        $nbEpisode = 0;
        foreach ($series as $serie) {
            if ($nbEpisode < count($serie->episode)) {
                $nbEpisode = count($serie->episode);
                $serieMostViewed = $serie;
            }

        }

        return $this->render('home/index.html.twig',[
            'serieWithMostComments' => $serieWithMostComments,
            'seriesRandom' => $seriesRandom,
            'serieMostLike' => $serieMostLike,
            'serieMostViewed' => $serieMostViewed
        ]);
    }
}
