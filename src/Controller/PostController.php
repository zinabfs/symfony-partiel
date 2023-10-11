<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Dislike;
use App\Entity\Like;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\DislikeRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/posts', name: 'posts')]
    public function getPosts(PostRepository $postRepository): Response
    {
        return $this->render('posts/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/posts/{id}', name: 'post', methods: ['GET', 'POST'])]
    public function getPost($id, PostRepository $postRepository, Request $request, EntityManagerInterface $entityManager, CommentRepository $commentRepository): Response
    {
        $post = $postRepository->findOneBy(['id' => $id]);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setContent($form->get('content')->getData());
            $comment->setPost($postRepository->findOneBy(['id' => $id]));
            $comment->setAuthor($this->getUser()->getLastName());

            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return $this->render('posts/post.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'comments' => $commentRepository->findBy(['post' => $post]),
        ]);
    }

    #[Route('/post/{id}/like', name: 'post_like')]
    public function like(PostRepository $postRepository, $id, EntityManagerInterface $manager, DislikeRepository $dislikeRepository, LikeRepository $likeRepository){

        $post = $postRepository->findOneBy(['id' => $id]);
        $user = $this->getUser();

        $dislikes = $dislikeRepository->findBy(["user" => $user, "post" => $post]);
        foreach($dislikes as $dislike){
            $manager->remove($dislike);
        }
        $manager->flush();

        dump($user);
        $like = new Like();
        $like->setPost($post)->setUser($user);
        $manager->persist($like);
        $manager->flush();

        return $this->redirectToRoute('post', ["id" => $id]);
    }

    #[Route('/post/{id}/dislike', name: 'post_dislike')]
    public function dislike(PostRepository $postRepository, $id, EntityManagerInterface $manager, LikeRepository $likeRepository){

        $post = $postRepository->findOneBy(['id' => $id]);
        $user = $this->getUser();

        $likes = $likeRepository->findBy(["user" => $user, "post" => $post]);
        foreach($likes as $like){
            $manager->remove($like);
        }
        $manager->flush();

        $dislike = new Dislike();
        $dislike->setPost($post)->setUser($user);
        $manager->persist($dislike);
        $manager->flush();

        return $this->redirectToRoute('post', ["id" => $id]);
    }
}
