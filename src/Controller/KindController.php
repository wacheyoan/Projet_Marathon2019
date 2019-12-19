<?php

namespace App\Controller;

use App\Entity\Kind;
use App\Form\KindType;
use App\Repository\KindRepository;
use App\Repository\SeriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/kind")
 */
class KindController extends AbstractController
{
    /**
     * @Route("/", name="kind_index", methods={"GET"})
     */
    public function index(KindRepository $kindRepository,SeriesRepository $seriesRepository): Response
    {

        $series = $seriesRepository->findAll();

        $kinds = [];
        foreach ($series as $serie){
            foreach ($serie->getKinds() as $kindSerie){

                $kinds[$kindSerie->getName()][] = $serie;
            }
        }

        $series = new ArrayCollection();
        foreach ($kinds as $kind) {
                do {
                    $serie = $kind[rand(0, sizeof($kind) - 1)];
                    unset($kind[array_search($serie, $kind)]);
                    $kind = array_values($kind);
                } while ($series->contains($serie) && sizeof($kind) > 0);

            $series[key($kinds)]= $serie;
            next($kinds);
        }
        return $this->render('kind/index.html.twig', [
            'series' => $series
        ]);
    }

    /**
     *
     * @IsGranted("ROLE_ADMIN")

     * @Route("/new", name="kind_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $kind = new Kind();
        $form = $this->createForm(KindType::class, $kind);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($kind);
            $entityManager->flush();

            return $this->redirectToRoute('kind_index');
        }

        return $this->render('kind/new.html.twig', [
            'kind' => $kind,
            'form' => $form->createView(),
        ]);
    }

    /**
     *
     * @IsGranted("ROLE_ADMIN")

     * @Route("/{id}", name="kind_show", methods={"GET"})
     */
    public function show(Kind $kind): Response
    {
        return $this->render('kind/show.html.twig', [
            'kind' => $kind,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")

     * @Route("/{id}/edit", name="kind_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Kind $kind): Response
    {
        $form = $this->createForm(KindType::class, $kind);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('kind_index');
        }

        return $this->render('kind/edit.html.twig', [
            'kind' => $kind,
            'form' => $form->createView(),
        ]);
    }

    /**
     *
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="kind_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Kind $kind): Response
    {
        if ($this->isCsrfTokenValid('delete'.$kind->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($kind);
            $entityManager->flush();
        }

        return $this->redirectToRoute('kind_index');
    }
}
