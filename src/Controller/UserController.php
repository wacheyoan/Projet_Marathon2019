<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Series;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     *
     * @Route("/profil", name="user_profil", methods={"GET"})
     *
     */
    public function profil(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $userRepository = $entityManager->getRepository(User::class);
        $user = $this->getUser();
        if($user) {
            $episodes = $user->getEpisodes();
            $series = new ArrayCollection();
            $nbEpisodesSeen = 0;
            $cumulativeTime = 0;
            foreach ($episodes as $episode) {
                $serie = $episode->getSeries();
                $nbEpisodesSeen ++;
                $cumulativeTime += $episode->getDuration();

                if (!$series->contains($serie)) {
                    $series[] = $serie;
                }
            }


            $commentRepository = $entityManager->getRepository(Comments::class);

            $comments = $commentRepository->findBy(['User' => $user]);

            $nbComments = count($comments);

        }


        return $this->render('user/profil.html.twig', [
            'series' => $series,
            'comments' => $comments,
            'nbEpisodesSeen' => $nbEpisodesSeen,
            'cumulativeTime' =>$cumulativeTime,
            'nbComments' => $nbComments,
        ]);

    }





    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['avatar']->getData();

            if($file !== null && $file instanceof UploadedFile){

                $fileInfo = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                try {
                    $fileName = \uniqid().\urldecode($fileInfo).'.'.$file->guessExtension();
                    $file->move(
                        $this->getParameter('users_directory'),
                        $fileName
                    );
                } catch (FileException $e){
                    $this->addFlash('danger', 'Error on FileUpload : '.$e->getMessage());

                    return $this->redirectToRoute('users_profil');
                }
                $user->setAvatar($fileName);
            }


            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_profil');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }



}
