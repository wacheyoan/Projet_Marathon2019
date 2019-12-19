<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Episodes;
use App\Entity\Kind;
use App\Entity\Series;
use App\Form\CommentsType;
use App\Form\SeriesType;
use App\Form\TheSearchType;
use App\Repository\CommentsRepository;
use App\Repository\EpisodesRepository;
use App\Repository\SeriesRepository;
use PhpParser\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/series")
 */
class SeriesController extends AbstractController
{
    /**
     * @Route("/", name="series_index", methods={"GET","POST"})
     */
    public function index(SeriesRepository $seriesRepository,Request $request,
                          EpisodesRepository $episodesRepository): Response
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
        return $this->render('series/index.html.twig', [
            'series' => $seriesRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     *
     * @IsGranted("ROLE_ADMIN")

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
     * @Route("/{id}", name="series_show", methods={"GET","POST"})
     */
    public function show(Series $series,Request $request,EpisodesRepository $episodesRepository,
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

        $comment = new Comments();

        $form = $this->createForm(CommentsType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setCreatedAt(new \DateTime());
            $comment->setSeries($series);
            $comment->setPositive(0);
            $comment->setValidated(0);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('series_show',['id'=>$series->getId()]);
        }


        return $this->render('series/show.html.twig', [
            'series' => $series,
            'seasons' => $tab,
            'comments' => $comments,
            'form' => $form->createView(),

        ]);
    }

    /**
     *
     * @IsGranted("ROLE_ADMIN")

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
     * @IsGranted("ROLE_ADMIN")

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
