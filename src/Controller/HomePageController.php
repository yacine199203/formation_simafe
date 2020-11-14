<?php

namespace App\Controller;

use App\Repository\SlidersRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="homePage")
     */
    public function index(CategoryRepository $categoryRepo,SlidersRepository $slidersRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits

        $sliders = $slidersRepo->findAll();
        return $this->render('home.html.twig', [
            'categorys' => $categorys,//drop-down nos produits
            'sliders' => $sliders,
        ]);
    }

    /***************************************************************************************************/

    /**
     * @Route("/{slug}", name="categorys")
     * 
     * @return Response
     */
    public function show($slug,CategoryRepository $categoryRepo)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $category = $categoryRepo->findOneBySlug($slug);
        return $this->render('categorysList.html.twig', [
            'categorys' => $categorys,//drop-down nos produits
            'category'=> $category,
            
        ]);
    }

}




