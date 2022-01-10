<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_list')]
    public function productList(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->render('product/products.html.twig', [
            'products' => $products,
        ]);
    }


    #[Route('/product/{id}', name: 'product_show')]
    public function productShow($id, ProductRepository $productRepository)
    {
        $product = $productRepository->find($id);
        return $this->render('product/product.html.twig', [
            'product' => $product,
        ]);
    }
}
