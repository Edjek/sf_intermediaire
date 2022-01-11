<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBrandController extends AbstractController
{
    #[Route('/admin/update/brand/{id}', name: 'admin_update_brand')]
    public function updateBrand(
        $id,
        BrandRepository $brandRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ) {

        $brand = $brandRepository->find($id);

        $brandForm = $this->createForm(BrandType::class, $brand);

        $brandForm->handleRequest($request);

        if ($brandForm->isSubmitted() && $brandForm->isValid()) {
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
    public function createBrand(EntityManagerInterface $entityManagerInterface, Request $request)
    {
        $brand = new Brand();

        $brandForm = $this->createForm(BrandType::class, $brand);

        $brandForm->handleRequest($request);

        if ($brandForm->isSubmitted() && $brandForm->isValid()) {
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
