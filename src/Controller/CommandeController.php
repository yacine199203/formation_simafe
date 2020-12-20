<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\JobRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    /**
     * @Route("/commande", name="commande")
     */
    public function index(CategoryRepository $categoryRepo,JobRepository $jobRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        return $this->render('commande/index.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
        ]);
    }

    /**
     * @Route("/commande/ajouter", name="addCommande")
     */
    public function addCommande(): Response
    {
    
        $commande= new Commande();
        $manager=$this->getDoctrine()->getManager();
        if(empty($commandes)){
            $i=0;
        }elseif(($commandes[0]->getCreatedAt()->format('m')<date('m') && $commandes[0]->getCreatedAt()->format('Y') == date('Y')) || ($commandes[0]->getCreatedAt()->format('m')>date('m') && $commandes[0]->getCreatedAt()->format('Y') < date('Y'))){
            $i=0;
        }else{
            $i=$commandes[0]->getCounter();
        }
        $ref= date('Y').date('m').str_pad(($i+1), 4, '0', STR_PAD_LEFT);
        $commande->setRef($ref);
        $commande->setUser($this->getUser());
        $commande->setCounter($i+1);
        $manager->persist($commande);
        $manager->flush();

        return $this->json(['code'=> 200, 'message'=>'ça marche',
        'data'=>'super'],200);
    }
}
