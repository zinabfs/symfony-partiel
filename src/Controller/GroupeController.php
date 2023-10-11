<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Entity\Post;
use App\Form\GroupeType;
use App\Form\PostType;
use App\Repository\GroupeRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupeController extends AbstractController
{
    #[Route('/groupe', name: 'groupe', methods: ['GET', 'POST'])]
    public function getGroupes(GroupeRepository $groupeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $groupes = $groupeRepository->findAll();

        $groupe = new Groupe();
        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupe->setCreatedAt(new \DateTimeImmutable());
            $groupe->setName($form->get('name')->getData());
            $groupe->setDescription($form->get('description')->getData());
            $groupe->setOwner($this->getUser()->getLastName());

            $entityManager->persist($groupe);
            $entityManager->flush();
        }

        return $this->render('groupe/index.html.twig', [
            'groupes' => $groupes,
            'form' => $form->createView()
        ]);
    }

}