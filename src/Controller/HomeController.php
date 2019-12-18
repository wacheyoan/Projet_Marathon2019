<?php

namespace App\Controller;

use App\Repository\CommentsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CommentsRepository $commentsRepository,UserRepository $userRepository)
    {


        $serieWithMostComments = $commentsRepository->findWithMostComments();
        $users = $userRepository->findAll();



        return $this->render('home/index.html.twig',[
            'serieWithMostComments' => $serieWithMostComments,
        ]);
    }
}
