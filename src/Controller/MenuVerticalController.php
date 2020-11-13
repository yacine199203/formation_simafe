<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MenuVerticalController extends AbstractController
{
    
    public function index(CategoryRepository $repo,ProductRepository $prepo)
    {   
        $pcat = $prepo->findAll();
        $categorys = $repo->findAll();
        return $this->render('menu_vertical.html.twig', [
        'pcat'=>$pcat,
        'categorys'=> $categorys
        ]);
    }
}
