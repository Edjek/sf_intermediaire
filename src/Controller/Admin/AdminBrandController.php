<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBrandController extends AbstractController
{
    #[Route('/admin/update/brand/{id}', name: 'admin_update_brand')]
    public function updateBrand(
        $id,
        BrandRepository $brandRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        SluggerInterface $sluggerInterface
    ) {

        $brand = $brandRepository->find($id);

        $brandForm = $this->createForm(BrandType::class, $brand);

        $brandForm->handleRequest($request);

        if ($brandForm->isSubmitted() && $brandForm->isValid()) {
            // On récupère le fichier
            $brandFile = $brandForm->get('media')->getData();

            if ($brandFile) {

                // On créée un nom unique à notre fichier à partir du nom original
                // Pour éviter tout problème de confusion

                // On récupère le nom original du fichier
                $originalFilename = pathinfo($brandFile->getClientOriginalName(), PATHINFO_FILENAME);

                // On utilise slug sur le nom original pour avoir un nom valide du fichier
                $safeFilename = $sluggerInterface->slug($originalFilename);

                // On ajoute un id unique au nom de l'image
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brandFile->guessExtension();

                // On déplace le fichier dans le dossier public/brand
                // la destination du fichier est enregistré dans 'images_directory'
                // qui est défini dans le fichier config\services.yaml

                $brandFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $brand->setMedia($newFilename);
            }
            $entityManagerInterface->persist($brand);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'La marque a été modifié'
            );

            return $this->redirectToRoute('brand_list');
        }

        return $this->render("admin/admin_brand/brandform.html.twig", ['brandForm' => $brandForm->createView()]);
    }

    #[Route('/admin/create/brand', name: 'admin_create_brand')]
    public function createBrand(EntityManagerInterface $entityManagerInterface, Request $request, SluggerInterface $sluggerInterface)
    {
        $brand = new Brand();

        $brandForm = $this->createForm(BrandType::class, $brand);

        $brandForm->handleRequest($request);

        if ($brandForm->isSubmitted() && $brandForm->isValid()) {
            // On récupère le fichier
            $brandFile = $brandForm->get('media')->getData();

            if ($brandFile) {

                // On créée un nom unique à notre fichier à partir du nom original
                // Pour éviter tout problème de confusion

                // On récupère le nom original du fichier
                $originalFilename = pathinfo($brandFile->getClientOriginalName(), PATHINFO_FILENAME);

                // On utilise slug sur le nom original pour avoir un nom valide du fichier
                $safeFilename = $sluggerInterface->slug($originalFilename);

                // On ajoute un id unique au nom de l'image
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brandFile->guessExtension();

                // On déplace le fichier dans le dossier public/brand
                // la destination du fichier est enregistré dans 'images_directory'
                // qui est défini dans le fichier config\services.yaml

                $brandFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $brand->setMedia($newFilename);
            }
            $entityManagerInterface->persist($brand);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'Une marque a été créé'
            );

            return $this->redirectToRoute("brand_list");
        }

        return $this->render("admin/admin_brand/brandform.html.twig", ['brandForm' => $brandForm->createView()]);
    }


    #[Route('/admin/delete/brand/{id}', name: 'admin_delete_brand')]
    public function deleteBrand($id, BrandRepository $brandRepository, EntityManagerInterface $entityManagerInterface)
    {
        $brand = $brandRepository->find($id);

        $entityManagerInterface->remove($brand);

        $entityManagerInterface->flush();

        $this->addFlash(
            'notice',
            'La marque a été supprimé'
        );

        return $this->redirectToRoute("brand_list");
    }
}
