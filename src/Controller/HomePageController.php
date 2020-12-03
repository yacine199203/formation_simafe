<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Form\ProductSearchType;
use App\Repository\JobRepository;
use App\Repository\ProductRepository;
use App\Repository\SlidersRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
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
     * @Route("/", name="homePage")
     */
    public function index(Request $request,CategoryRepository $categoryRepo,SlidersRepository $slidersRepo,JobRepository $jobRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
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
            'categorys' => $categorys,//drop-down nos produits
            'sliders' => $sliders,
            'jobs' => $jobs,
            'newsletterForm'=> $newsletterForm->createView(),
        ]);
    }

    /**
     * permet de se désabonner de la newsletter
     * @Route("/se-desabonner", name="unsubscribeNewsletter")
     */
    public function showNewsletter(NewsletterRepository $newsletterRepo,CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
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
                $compare ='null';
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
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'form'=> $form->createView(),
        ]);
    }

    /**
     * permet de voir la page recrutement
     * @Route("/recrutement", name="recruitment")
     * 
     * @return Response
     */
    public function recruitement(RecruitementRepository $recruitementRepo,CategoryRepository $categoryRepo,JobRepository $jobRepo)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $recruitement = $recruitementRepo->findAll();
        return $this->render('recruitement.html.twig', [
            'categorys' => $categorys,//drop-down nos produits
            'jobs' => $jobs,
            'recruitement' => $recruitement,
        ]);
    }
    /**
     * permet de voir la page conditions
     * @Route("/recrutement/{id}", name="condition")
     * 
     * @return Response
     */
    public function conditions($id,RecruitementRepository $recruitementRepo,CategoryRepository $categoryRepo,JobRepository $jobRepo)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $recruitement = $recruitementRepo->findOneById($id);
        return $this->render('conditions.html.twig', [
            'categorys' => $categorys,//drop-down nos produits
            'jobs' => $jobs,
            'recruitement' => $recruitement,
        ]);
    }

     /**
     * permet de voir la page recherche
     * @Route("/recherche", name="search")
     * 
     * @return Response
     */
    public function search(ProductRepository $productRepo,Request $request,CategoryRepository $categoryRepo,JobRepository $jobRepo)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
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
            'categorys' => $categorys,//drop-down nos produits
            'jobs' => $jobs,
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




