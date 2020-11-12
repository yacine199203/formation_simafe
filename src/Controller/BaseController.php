<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    
    public function index(CategoryRepository $categoryRepo): Response
    {
        $categorys = $categoryRepo->findAll();
        return $this->render('home.html.twig', [
            'categorys' => $categorys,
        ]);
    }
}
