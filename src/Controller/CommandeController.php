<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Commande;
use App\Repository\JobRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    /**
     * @Route("/commande", name="commande")
     */
    public function index(CategoryRepository $categoryRepo,JobRepository $jobRepo,CommandeRepository $commandeRepo,Request $request): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $commandes= $commandeRepo->findAll();
        $session = $request->getSession();
        return $this->render('commande/index.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'commandes'=>$commandes,
            'session'=>$session,
        ]);
    }

    /**
     * @Route("/commande/panier", name="cart")
     */
    public function cart(CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request,ProductRepository $productRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $session = $request->getSession();       
        $cart = $session->get('cart',[]);
        $inCart=[];
        foreach($cart as $id=>$qty)
        {
            $inCart[]=[
                'product'=>$productRepo->find($id),
                'qty'=>$qty
            ];
        }
        return $this->render('commande/cart.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'items' => $inCart,
        ]);
    }

    /**
     * @Route("/commande/panier/ajouter/{id}", name="addCart")
     */
    public function addCart($id,Request $request): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart',[]);
        if(!empty($cart[$id]))
        {
            $cart[$id]++;
        }else
        {
            $cart[$id]=1;
        }
        $session->set('cart',$cart);
        return $this-> redirectToRoute('cart'); 
    }

    /**
     * @Route("/commande/panier/supprimer/{id}", name="removeCart")
     */
    public function removeCart($id,Request $request): Response
    {
        
        $session = $request->getSession();
        $cart = $session->get('cart',[]);
        if(!empty($cart[$id]))
        {
            unset($cart[$id]);
        }
        $session->set('cart',$cart);
        return $this-> redirectToRoute('cart'); 
    }

    /**
     * @Route("/commande/ajouter/{product}/{qty}", name="addCommande")
     */
    public function addCommande($product,$qty,CommandeRepository $commandeRepo,Request $request): Response
    {
    
        $p=explode(',',$product);
        $q=explode(',',$qty);
        $session = $request->getSession();
        $commandes = $commandeRepo->findBy(array(), array('id' => 'DESC'));
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
        $commande->setValid(false);
        for($j=0;$j<count($p);$j++)
        {
        $inCart= new Cart();
        $inCart->setProduct($p[$j]);
        $inCart->setQte($q[$j]);
        $commande->addCart($inCart);
        }
        $manager->persist($commande);
        $manager->flush();
        $session->set('cart',[]);
        return $this-> redirectToRoute('commande');
    }

    /**
     * @Route("/commande/valider/{id}", name="validateCommande")
     */
    public function validateCommande($id,CommandeRepository $commandeRepo,Request $request): Response
    {
        $valid= $commandeRepo->findOneById($id);
        $valid->setValid(true);
        $manager=$this->getDoctrine()->getManager();
        $manager->persist($valid);
        $manager->flush();
        return $this-> redirectToRoute('commande');
    }

    /**
     * permet de voir une commande
     * @Route("/commande/ma-commande/{id} ", name="showCommande")
     * @return Response
     */
    public function showeCommande($id,CategoryRepository $categoryRepo,JobRepository $jobRepo,CommandeRepository $commandeRepo)
    {   

        $showCommande = $commandeRepo->findOneById($id);
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        
        return $this->render('commande/showCommande.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'showCommande' => $showCommande,
        ]);       
        
    }

    /**
     * permet de supprimer une commande
     * @Route("/commande/supprimer/{id} ", name="removeCommande")
     * @return Response
     */
    public function removeCommande($id,CommandeRepository $commandeRepo)
    {   

        $removeCommande = $commandeRepo->findOneById($id);
        if($removeCommande->getValid()== false)
        {
            $manager=$this->getDoctrine()->getManager();
            $manager->remove($removeCommande); 
            $manager->flush();
            $this->addFlash(
                'success',
                "La commande ".$removeCommande->getRef()." a bien été supprimée "
            );
        }
        return $this-> redirectToRoute('commande');         
        
    }
}
