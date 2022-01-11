<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminProductController extends AbstractController
{
    #[Route('/admin/update/product/{id}', name: 'admin_update_product')]
    public function updateProduct(
        $id,
        ProductRepository $productRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ) {

        $product = $productRepository->find($id);

        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $entityManagerInterface->persist($product);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'La categorie a été modifié'
            );

            return $this->redirectToRoute('product_list');
        }

        return $this->render("admin/admin_product/productform.html.twig", ['productForm' => $productForm->createView()]);
    }

    #[Route('/admin/create/product', name: 'admin_create_product')]
    public function createProduct(EntityManagerInterface $entityManagerInterface, Request $request)
    {
        $product = new Product();

        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $entityManagerInterface->persist($product);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'Une categorie a été créé'
            );

            return $this->redirectToRoute("product_list");
        }

        return $this->render("admin/admin_product/productform.html.twig", ['productForm' => $productForm->createView()]);
    }


    #[Route('/admin/delete/product', name: 'admin_delete_product')]
    public function deleteProduct($id, ProductRepository $productRepository, EntityManagerInterface $entityManagerInterface)
    {
        $product = $productRepository->find($id);

        $entityManagerInterface->remove($product);

        $entityManagerInterface->flush();

        $this->addFlash(
            'notice',
            'La categorie a été supprimé'
        );

        return $this->redirectToRoute("product_list");
    }
}
