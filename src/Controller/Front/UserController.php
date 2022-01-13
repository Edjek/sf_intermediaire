<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/create/user', name: 'create_user')]
    public function index(
        EntityManagerInterface $entityManagerInterface,
        Request $request,
    UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $user = new User();
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid) {


            $user->setRoles(['USER_NAME']);
            $user->setDate(new \DateTime("NOW"));
            $plainPassword = $userForm->get('password')->getData();
            $hashPassword = $userPasswordHasherInterface->hashPassword($user, $plainPassword);
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'Un produit a été créé'
            );
            $this->redirectToRoute('');
        }
        return $this->render('front/user/userform.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }
}
