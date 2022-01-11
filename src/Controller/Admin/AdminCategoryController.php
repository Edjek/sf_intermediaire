<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCategoryController extends AbstractController
{
    #[Route('/admin/update/category/{id}', name: 'admin_update_category')]
    public function updateCategory(
        $id,
        CategoryRepository $categoryRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ) {

        $category = $categoryRepository->find($id);

        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'La categorie a été modifié'
            );

            return $this->redirectToRoute('category_list');
        }

        return $this->render("admin/admin_category/categoryform.html.twig", ['categoryForm' => $categoryForm->createView()]);
    }

    #[Route('/admin/create/category', name: 'admin_create_category')]
    public function createCategory(EntityManagerInterface $entityManagerInterface, Request $request, SluggerInterface $sluggerInterface)
    {
        $category = new Category();

        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            // On récupère le fichier
            $categoryFile = $categoryForm->get('media')->getData();

            if ($categoryFile) {

                // On créée un nom unique à notre fichier à partir du nom original
                // Pour éviter tout problème de confusion

                // On récupère le nom original du fichier
                $originalFilename = pathinfo($categoryFile->getClientOriginalName(), PATHINFO_FILENAME);

                // On utilise slug sur le nom original pour avoir un nom valide du fichier
                $safeFilename = $sluggerInterface->slug($originalFilename);

                // On ajoute un id unique au nom de l'image
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $categoryFile->guessExtension();

                // On déplace le fichier dans le dossier public/category
                // la destination du fichier est enregistré dans 'images_directory'
                // qui est défini dans le fichier config\services.yaml

                $categoryFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $category->setMedia($newFilename);
            }
            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'Une categorie a été créé'
            );

            return $this->redirectToRoute("category_list");
        }

        return $this->render("admin/admin_category/categoryform.html.twig", ['categoryForm' => $categoryForm->createView()]);
    }


    #[Route('/admin/delete/category/{id}', name: 'admin_delete_category')]
    public function deleteCategory($id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManagerInterface)
    {
        $category = $categoryRepository->find($id);

        $entityManagerInterface->remove($category);

        $entityManagerInterface->flush();

        $this->addFlash(
            'notice',
            'La categorie a été supprimé'
        );

        return $this->redirectToRoute("category_list");
    }
}
