<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Form\ProductSearchType;
use App\Repository\JobRepository;
use App\Repository\ProductRepository;
use App\Repository\SlidersRepository;
use App\Repository\CategoryRepository;
use App\Repository\JobProductRepository;
use App\Repository\NewsletterRepository;
use App\Repository\RecruitementRepository;
use App\Repository\ProductionJobRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomePageController extends AbstractController
{
    /**
     * page d'acceuil
     * @Route("/", name="homePage")
     */
    public function index(Request $request,SlidersRepository $slidersRepo): Response
    {
        $sliders = $slidersRepo->findAll();
        $newsletter = new Newsletter();
        $newsletterForm = $this->createForm(NewsletterType::class,$newsletter);
        $newsletterForm-> handleRequest($request);
        if($newsletterForm->isSubmitted() && $newsletterForm->isValid())
        {
            $manager=$this->getDoctrine()->getManager();
            $newsletter->setStatus(false);
            $newsletter->setUnsubscribe(false);
            $manager->persist($newsletter); 
            $manager->flush();
            return $this-> redirectToRoute('homePage');
        }   
        return $this->render('home.html.twig', [
            'sliders' => $sliders,
            'newsletterForm'=> $newsletterForm->createView(),
        ]);
    }

    /**
     * permet de se désabonner de la newsletter
     * @Route("/se-desabonner", name="unsubscribeNewsletter")
     */
    public function showNewsletter(NewsletterRepository $newsletterRepo,Request $request): Response
    {
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('email', EmailType::class,['label' => 'Email :','attr'=>[
                'placeholder'=>'Votre email' ]   
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $customer= $form->get('email')->getData();
            $unsubscribe = $newsletterRepo->findOneByEmail($customer);
            if($unsubscribe==null)
            {
                $compare = null;
            }else{
                $compare =$unsubscribe->getEmail();
            }
            if($customer == $compare)
            {
                $unsubscribe->setUnsubscribe(true);
                $manager=$this->getDoctrine()->getManager();;
                $manager->persist($unsubscribe); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Votre demande de désabonnement a été prise en compte"
                );
                return $this-> redirectToRoute('homePage');
            }else{
                $this->addFlash(
                    'danger',
                    "Cette adresse mail n'existe pas"
                );
            }
        }
        return $this->render('/unsubscribe.html.twig', [
            'form'=> $form->createView(),
        ]);
    }

    /**
     * permet de voir la page recrutement
     * @Route("/recrutement", name="recruitment")
     * 
     * @return Response
     */
    public function recruitement(RecruitementRepository $recruitementRepo)
    {
        $recruitement = $recruitementRepo->findAll();
        return $this->render('recruitement.html.twig', [
            'recruitement' => $recruitement,
        ]);
    }
    /**
     * permet de voir la page conditions
     * @Route("/recrutement/{id}", name="condition")
     * 
     * @return Response
     */
    public function conditions($id,RecruitementRepository $recruitementRepo)
    {
        
        $recruitement = $recruitementRepo->findOneById($id);
        return $this->render('conditions.html.twig', [
            'recruitement' => $recruitement,
        ]);
    }

     /**
     * permet de voir la page recherche
     * @Route("/recherche", name="search")
     * 
     * @return Response
     */
    public function search(ProductRepository $productRepo,Request $request)
    {
        $form = $this->createForm(ProductSearchType::class);
        $form-> handleRequest($request);
        $product=null;
        $result=null;
        $message = null;
        $manager=$this->getDoctrine()->getManager();
        if($form->isSubmitted() && $form->isValid())
        {
            $result=$form->get('word')->getData();
            $product=$manager->createQuery('SELECT p FROM App\Entity\Product p WHERE p.productName LIKE \'%'.$result.'%\'')->getResult();
            if($product == null)
            {
                $message="Votre recherche <strong>\"".$result."\"</strong> n'a donné aucun résultat.";  
            }
        }
        return $this->render('search.html.twig', [
            'product' => $product,
            'result'=>$result,
            'message'=>$message,
            'form'=> $form->createView(),
        ]);
    }

    /***************************************************************************************************/

    /**
     * permet de voir la liste des produits dans une catégorie
     * @Route("/categorie/{slug}", name="categoryproduct")
     * 
     * @return Response
     */
    public function showCategoryProduct($slug,CategoryRepository $categoryRepo)
    {
        $category = $categoryRepo->findOneBySlug($slug);
        return $this->render('categoryProductList.html.twig', [
            'category'=> $category,
        ]);
    }


    /**
     * permet de voir la présentation du produit
     * @Route("/categorie/{slug}/{productSlug}", name="productPresentation")
     * 
     * @return Response
     */
    public function showProductPresentation($slug,$productSlug,CategoryRepository $categoryRepo,ProductRepository $productRepo)
    {
        $category = $categoryRepo->findOneBySlug($slug);
        $product=$productRepo->findOneBySlug($productSlug);
        return $this->render('productPresentation.html.twig', [
            'category'=> $category,
            'product'=> $product,
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
        $jobs = $jobRepo->findOneBySlug($metier);
        $jps = $jpRepo->findAll();
        $products =$productRepo->findAll();
        return $this->render('jobProductList.html.twig', [
            'jobs'=> $jobs,
            'jps'=> $jps,
            'products'=> $products,
            
        ]);
    }

    /**
     * permet de voir la réalisation
     * @Route("/realisation/{slug}", name="production")
     * 
     * @return Response
     */
    public function showProduction($slug,CategoryRepository $categoryRepo,JobRepository $jobRepo,ProductionJobRepository $pjRepo)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $jb = $jobRepo->findOneBySlug($slug);
        
        
        return $this->render('production.html.twig', [
            'categorys' => $categorys,//drop-down nos produits
            'jb'=> $jb,
            'jobs'=> $jobs,
           
        ]);
    }


     
}




