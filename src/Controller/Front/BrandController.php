<?php

namespace App\Controller\Front;

use App\Repository\BrandRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BrandController extends AbstractController
{
    #[Route('/brands', name: 'brand_list')]
    public function brand_list(BrandRepository $brandRepository): Response
    {
        $brands = $brandRepository->findAll();

        return $this->render('front/brand/brands.html.twig', [
            'brands' => $brands,
        ]);
    }

    #[Route('/brand/{id}', name: 'brand_show')]
    public function brandShow($id, BrandRepository $brandRepository)
    {
        $brand = $brandRepository->find($id);
        return $this->render('front/brand/brand.html.twig', [
            'brand' => $brand,
        ]);
    }
}
