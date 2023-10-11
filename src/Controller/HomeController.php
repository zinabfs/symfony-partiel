<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(PostRepository $postRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUser($this->getUser());
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setAuthor($this->getUser()->getLastName());

            /** @var UploadedFile $imageFile */
            $imageFile = $form['imageFile']->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    'uploads',
                    $newFilename
                );

                $post->setImage($newFilename);
            }

            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }


        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $postRepository->findAll(),
        ]);
    }
}
