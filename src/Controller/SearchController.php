<?php

namespace App\Controller;

use App\Form\TheSearchType;
use App\Repository\EpisodesRepository;
use App\Repository\KindRepository;
use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/search")
 */
class SearchController extends AbstractController
{
    /**
     * @Route("/", name="search_index", methods={"GET","POST"})
     */
    public function index(Request $request, EpisodesRepository $episodesRepository)
    {
        $form = $this->createForm(TheSearchType::class);
        $form->handleRequest($request);
        $search ='';

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $like = $data['Recherche'];

            $search = $episodesRepository->findByLike($like);

            return $this->render('series/index.html.twig',[
                'search' => $search,
            ]);
        }
        return $this->render('search/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }




}
