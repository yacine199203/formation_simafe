<?php

namespace App\Controller;

use App\Repository\JobRepository;
use App\Repository\ProductRepository;
use App\Repository\SlidersRepository;
use App\Repository\CategoryRepository;
use App\Repository\JobProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="homePage")
     */
    public function index(CategoryRepository $categoryRepo,SlidersRepository $slidersRepo,JobRepository $jobRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits

        $jobs = $jobRepo->findAll();
        $sliders = $slidersRepo->findAll();
        return $this->render('home.html.twig', [
            'categorys' => $categorys,//drop-down nos produits
            'sliders' => $sliders,
            'jobs' => $jobs,
        ]);
    }

    /***************************************************************************************************/

    /**
     * permet de voir la liste des produits dans une catégorie
     * @Route("/categorie/{slug}", name="categoryproduct")
     * 
     * @return Response
     */
    public function showCategoryProduct($slug,CategoryRepository $categoryRepo,JobRepository $jobRepo)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $category = $categoryRepo->findOneBySlug($slug);
        $jobs = $jobRepo->findAll();
        return $this->render('categoryProductList.html.twig', [
            'categorys' => $categorys,//drop-down nos produits
            'category'=> $category,
            'jobs' => $jobs,
        ]);
    }


    /**
     * permet de voir la présentation du produit
     * @Route("/categorie/{slug}/{productSlug}", name="productPresentation")
     * 
     * @return Response
     */
    public function showProductPresentation($slug,$productSlug,CategoryRepository $categoryRepo,ProductRepository $productRepo,JobRepository $jobRepo)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $category = $categoryRepo->findOneBySlug($slug);
        $product=$productRepo->findOneBySlug($productSlug);
        return $this->render('productPresentation.html.twig', [
            'categorys' => $categorys,//drop-down nos produits
            'category'=> $category,
            'product'=> $product,
            'jobs'=> $jobs,
        ]);
    }

    /**
     * permet de voir la liste des produits dans un métier
     * @Route("/metier/{metier}", name="jobProduct")
     * 
     * @return Response
     */
    public function showJobProduct($metier,CategoryRepository $categoryRepo,JobRepository $jobRepo,JobProductRepository $jpRepo,ProductRepository $productRepo)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findOneBySlug($metier);
        $jps = $jpRepo->findAll();
        $products =$productRepo->findAll();
        return $this->render('jobProductList.html.twig', [
            'categorys' => $categorys,//drop-down nos produits
            'jobs'=> $jobs,
            'jps'=> $jps,
            'products'=> $products,
            
        ]);
    }


}




