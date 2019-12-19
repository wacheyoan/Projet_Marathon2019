<?php

namespace App\Controller;

use App\Entity\Episodes;
use App\Entity\Kind;
use App\Entity\Series;
use App\Form\SeriesType;
use App\Repository\CommentsRepository;
use App\Repository\EpisodesRepository;
use App\Repository\SeriesRepository;
use PhpParser\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/series")
 */
class SeriesController extends AbstractController
{
    /**
     * @Route("/", name="series_index", methods={"GET"})
     */
    public function index(SeriesRepository $seriesRepository): Response
    {
        return $this->render('series/index.html.twig', [
            'series' => $seriesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="series_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $series = new Series();
        $form = $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($series);
            $entityManager->flush();

            return $this->redirectToRoute('series_index');
        }

        return $this->render('series/new.html.twig', [
            'series' => $series,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="series_show", methods={"GET"})
     */
    public function show(Series $series,EpisodesRepository $episodesRepository,
                         CommentsRepository $commentsRepository): Response
    {
        $episodes = $episodesRepository->findBy([
            'Series' => $series
        ]);
        $comments = $commentsRepository->findBy([
            'Series' => $series
        ]);

        $tab = [];
        /** @var Episodes $episode */
        foreach ($episodes as $episode) {
            $tab["Season".$episode->getSeason()][$episode->getNumber()] = $episode;
        }

        
        return $this->render('series/show.html.twig', [
            'series' => $series,
            'seasons' => $tab,
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/{id}/edit", name="series_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Series $series): Response
    {
        $form = $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('series_index');
        }

        return $this->render('series/edit.html.twig', [
            'series' => $series,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="series_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Series $series): Response
    {
        if ($this->isCsrfTokenValid('delete'.$series->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($series);
            $entityManager->flush();
        }

        return $this->redirectToRoute('series_index');
    }

    /**
     * @Route("/kind/{kind}", name="series_kind_show", methods={"GET"})
     */
    public function showByKind(Request $request,String $kind) :Response{

        $entityManager = $this->getDoctrine()->getManager();

        $seriesRepository = $entityManager->getRepository(Series::class);
        $series = $seriesRepository->findByKind($kind);

        return $this->render('series/index.html.twig', [
            'series' => $series,
        ]);
    }
}
