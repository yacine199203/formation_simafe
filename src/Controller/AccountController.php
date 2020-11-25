<?php

namespace App\Controller;

use App\Repository\JobRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * permet de se connecter
     * @Route("/login", name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils, CategoryRepository $categoryRepo,JobRepository $jobRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $error = $utils->getLastAuthenticationError();
        $userName = $utils->getLastUsername();
        return $this->render('account/login.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'error' => $error !== null,
            'userName' => $userName,
        ]);
    }

    /**
     * permet de se deconnecter
     * @Route("/logout", name="account_logout")
     * @return Response
     */
    public function logout(): Response
    {
        
    }

    
}
