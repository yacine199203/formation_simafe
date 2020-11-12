<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="homePage")
     */
    public function index(CategoryRepository $categoryRepo): Response
    {
        $categorys = $categoryRepo->findAll();
        return $this->render('home.html.twig', [
            'categorys' => $categorys,
        ]);
    }
}
