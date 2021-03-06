<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminMediaController extends AbstractController
{
    #[Route('/admin/update/media/{id}', name: 'admin_update_media')]
    public function updateMedia(
        $id,
        MediaRepository $mediaRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        SluggerInterface $sluggerInterface
    ) {

        $media = $mediaRepository->find($id);

        $mediaForm = $this->createForm(MediaType::class, $media);

        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {
            // On récupère le fichier
            $mediaFile = $mediaForm->get('src')->getData();

            if ($mediaFile) {

                // On créée un nom unique à notre fichier à partir du nom original
                // Pour éviter tout problème de confusion

                // On récupère le nom original du fichier
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);

                // On utilise slug sur le nom original pour avoir un nom valide du fichier
                $safeFilename = $sluggerInterface->slug($originalFilename);

                // On ajoute un id unique au nom de l'image
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();

                // On déplace le fichier dans le dossier public/media
                // la destination du fichier est enregistré dans 'images_directory'
                // qui est défini dans le fichier config\services.yaml

                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $media->setSrc($newFilename);
            }
            $entityManagerInterface->persist($media);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'L\'image a été modifié'
            );

            return $this->redirectToRoute('product_list');
        }

        return $this->render("admin/admin_media/mediaform.html.twig", ['mediaForm' => $mediaForm->createView()]);
    }

    #[Route('admin/create/media', name: 'admin_create_media')]
    public function createMedia(EntityManagerInterface $entityManagerInterface, Request $request,  SluggerInterface $sluggerInterface)
    {
        $media = new Media();

        $mediaForm = $this->createForm(MediaType::class, $media);

        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {
            // On récupère le fichier
            $mediaFile = $mediaForm->get('src')->getData();

            if ($mediaFile) {

                // On créée un nom unique à notre fichier à partir du nom original
                // Pour éviter tout problème de confusion

                // On récupère le nom original du fichier
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);

                // On utilise slug sur le nom original pour avoir un nom valide du fichier
                $safeFilename = $sluggerInterface->slug($originalFilename);

                // On ajoute un id unique au nom de l'image
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();

                // On déplace le fichier dans le dossier public/media
                // la destination du fichier est enregistré dans 'images_directory'
                // qui est défini dans le fichier config\services.yaml

                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $media->setSrc($newFilename);
            }
            $entityManagerInterface->persist($media);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'Une image a été créé'
            );

            return $this->redirectToRoute("product_list");
        }

        return $this->render("admin/admin_media/mediaform.html.twig", ['mediaForm' => $mediaForm->createView()]);
    }
}
