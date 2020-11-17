<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\JobType;
use App\Entity\Product;
use App\Entity\Sliders;
use App\Entity\Category;
use App\Form\ProductType;
use App\Form\SlidersType;
use App\Form\CategoryType;
use Cocur\Slugify\Slugify;
use App\Repository\JobRepository;
use App\Repository\ProductRepository;
use App\Repository\SlidersRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashbordController extends AbstractController
{

    /**
     * @Route("/dashbord", name="dashbord")
     */
    public function index(CategoryRepository $categoryRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits

        return $this->render('/dashbord/index.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
        ]);
    }

/***************************************************************************************************/

    /**
     * @Route("/dashbord/categorie", name="category")
     */
    public function showCategory(CategoryRepository $categoryRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits

        return $this->render('/dashbord/showCategory.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
        ]);
    }

    /**
     * permet d'ajouter une catégorie 
     * @Route("/dashbord/ajouter-categorie", name="addCategory")
     * @return Response
     */
    public function addCategory(CategoryRepository $categoryRepo,Request $request)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $addCategory = new Category();
        $addCatForm = $this->createForm(CategoryType::class,$addCategory);
        $addCatForm-> handleRequest($request);
        if($addCatForm->isSubmitted() && $addCatForm->isValid())
        {
            $file= $addCategory->getImage();
            $fileName=  md5(uniqid()).'.'.$file->guessExtension();
            if($file->guessExtension()!='png'){
                $this->addFlash(
                    'danger',
                    "votre image doit être en format png "
                );  
            }else
            {
                    $file->move($this->getParameter('upload_directory_png'),$fileName);
                    $addCategory->setImage($fileName);
                    $manager=$this->getDoctrine()->getManager();
                    $manager->persist($addCategory); 
                    $manager->flush();
                    $this->addFlash(
                        'success',
                        "La catégorie ".$addCategory->getCategoryName()." a bien été ajoutée "
                );
                return $this-> redirectToRoute('category');
            }
        } 
        
        return $this->render('dashbord/addCategory.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'addCatForm'=> $addCatForm->createView(),
        ]);
    }

    /**
     * permet de modifier une catégorie
     * @Route("/dashbord/modifier-categorie/{categoryName} ", name="editCategory")
     * @return Response
     */
    public function editCategory($categoryName,CategoryRepository $categoryRepo,Request $request)
    {   
        $categorys = $categoryRepo->findAll();//drop-down nos produits

        $editCategory = $categoryRepo->findOneBySlug($categoryName);
        $editCatForm = $this->createForm(CategoryType::class,$editCategory);
        $editCatForm-> handleRequest($request);
        if($editCatForm->isSubmitted() && $editCatForm->isValid())
        {
            $file= $editCategory->getImage();
            $fileName=  md5(uniqid()).'.'.$file->guessExtension();
            if($file->guessExtension()!='png'){
                $this->addFlash(
                    'danger',
                    "votre image doit être en format png "
                );  
            }else
            {
                $file->move($this->getParameter('upload_directory_png'),$fileName);
                $editCategory->setImage($fileName);
                $manager=$this->getDoctrine()->getManager();
                $manager->persist($editCategory); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    "La catégorie ".$editCategory->getCategoryName()." a bien été modifiée "
                );
                return $this-> redirectToRoute('category');
            }
        } 
        
        return $this->render('dashbord/editCategory.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'editCatForm'=> $editCatForm->createView(),
        ]);
    }

    /**
     * permet de supprimer une catégorie
     * @Route("/dashbord/supprimer-categorie/{categoryName} ", name="removeCategory")
     * @return Response
     */
    public function removeCategory($categoryName,CategoryRepository $categoryRepo,ProductRepository $productRepo,Request $request)
    {   
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $products = $productRepo->findByCategory($categoryName);
        $removeCategory = $categoryRepo->findOneBySlug($categoryName);
        $file= $removeCategory->getImage();
        unlink('../public/images/'.$file);
        foreach ($products as $productPng){
            unlink('../public/images/'.$prod->getPng());
        }
        foreach ($products as $productImage){
            unlink('../public/fiches-technique/'.$productImage->getImage());
        }
        foreach ($products as $productPdf){
            unlink('../public/fiches-technique/'.$productPdf->getPdf());
        }
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeCategory); 
        $manager->flush();
            $this->addFlash(
                'success',
                "La catégorie ".$removeCategory->getCategoryName()." a bien été supprimée "
            );
            return $this-> redirectToRoute('category');
         
        
        return $this->render('dashbord/editCategory.html.twig', [
            
        ]);
    }

/***************************************************************************************************/

    /**
     * permet de voir la page des sliders
     * @Route("/dashbord/sliders", name="sliders")
     */
    public function showSliders(CategoryRepository $categoryRepo,SlidersRepository $slidersRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits

        $sliders = $slidersRepo->findAll();
        return $this->render('/dashbord/showSliders.html.twig', [
            'categorys' => $categorys, //drop-down nos produits

            'sliders' => $sliders,
        ]);
    }


    /**
     * permet d'ajouter un slider
     * @Route("/dashbord/ajouter-slid", name="addSlid")
     * @return Response
     */
    public function addSlid(CategoryRepository $categoryRepo,Request $request)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits

        $addSlider = new Sliders();
        $addSlidForm = $this->createForm(SlidersType::class,$addSlider);
        $addSlidForm-> handleRequest($request);
        if($addSlidForm->isSubmitted() && $addSlidForm->isValid()){
            $file= $addSlider->getImage();
            $fileName=  md5(uniqid()).'.'.$file->guessExtension();
            if($file->guessExtension()!='png'){
                $this->addFlash(
                    'danger',
                    "votre image doit être en format png "
                );  
            }else{
                $file->move($this->getParameter('upload_directory_png'),$fileName);
                $addSlider->setImage($fileName);
                $manager=$this->getDoctrine()->getManager();
                $manager->persist($addSlider); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Le slid a bien été ajouté "
                );
                return $this-> redirectToRoute('sliders');
            } 
        }
        return $this->render('dashbord/addSlid.html.twig', [
            'categorys' => $categorys, //drop-down nos produits

            'addSlidForm'=> $addSlidForm->createView(),
        ]);
    }

    /**
     * permet de supprimer un slid
     * @Route("/dashbord/supprimer-slid/{image} ", name="removeSlid")
     * @return Response
     */
    public function removeSlid($image,SlidersRepository $slidersRepo)
    {   
        $removeslid = $slidersRepo->findOneById($image);
        $file= $removeslid->getImage();
        unlink('../public/images/'.$file);
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeslid); 
        $manager->flush();
        $this->addFlash(
            'success',
            "Le slid a bien été supprimé "
        );
        return $this-> redirectToRoute('sliders');
        return $this->render('dashbord/index.html.twig', [
            
        ]);
    }

/***************************************************************************************************/

    /**
     * permet de voir la page des produits
     * @Route("/dashbord/produits", name="products")
     */
    public function showProducts(CategoryRepository $categoryRepo,ProductRepository $productRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $products = $productRepo->findAll();
        return $this->render('/dashbord/showProduct.html.twig', [
            'categorys' => $categorys, //drop-down nos produits

            'products' => $products,
        ]);
    }

    /**
      * permet d'ajouter un produit
     * @Route("/dashbord/ajouter-produit", name="addProduct")
     * @return Response
     */
    public function addProduct(CategoryRepository $categoryRepo,Request $request)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $addProduct = new Product();
        $addProdForm = $this->createForm(ProductType::class,$addProduct);
        $addProdForm-> handleRequest($request);
        if($addProdForm->isSubmitted() && $addProdForm->isValid()){
            $slugify= new Slugify();
            $filePng= $addProduct->getPng();
            $filePdf= $addProduct->getPdf();
            $fileImage= $addProduct->getImage();
            $fileNamePng=  $slugify-> slugify($addProduct->getProductName()).'.'.$filePng->guessExtension();
            $fileNamePdf=  $slugify-> slugify($addProduct->getProductName()).'.'.$filePdf->guessExtension();
            $fileNameImage=  $slugify-> slugify($addProduct->getProductName()).'g.'.$fileImage->guessExtension();
            if($filePng->guessExtension()!='png'||$fileImage->guessExtension()!='png'){
                $this->addFlash(
                    'danger',
                    "votre image doit être en format png "
                );  
            }elseif($filePdf->guessExtension()!='pdf')
            {
                $this->addFlash(
                    'danger',
                    "votre fichier doit être en format pdf "
                );  
            }else{
                $filePng->move($this->getParameter('upload_directory_png'),$fileNamePng);
                $fileImage->move($this->getParameter('upload_directory_png'),$fileNameImage);
                $filePdf->move($this->getParameter('upload_directory_pdf'),$fileNamePdf);
                $addProduct->setPng($fileNamePng);
                $addProduct->setPdf($fileNamePdf);
                $addProduct->setImage($fileNameImage);
                $manager=$this->getDoctrine()->getManager();
                foreach ($addProduct->getCharacteristics() as $chara){
                    $chara->setProduct($addProduct);
                    $manager->persist($chara);
                }
                foreach ($addProduct->getJobProducts() as $jp){
                    $jp->setProduct($addProduct);
                    $manager->persist($jp);
                }
                $manager->persist($addProduct); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Le produit ".$addProduct->getProductName()." a bien été ajouté "
                );
                return $this-> redirectToRoute('products');
            } 
        }
        return $this->render('dashbord/addProduct.html.twig', [
            'categorys'=> $categorys,//drop-down nos produits
            'addProdForm'=> $addProdForm->createView(),
            
        ]);
    }

    /**
     * permet de modifier un produit
     * @Route("/dashbord/modifier-produit/{productName} ", name="editProduct")
     * @return Response
     */
    public function editprod($productName,CategoryRepository $categoryRepo,ProductRepository $productRepo,Request $request)
    {   $categorys = $categoryRepo->findAll();//drop-down nos produits
        $editProduct = $productRepo->findOneBySlug($productName);
        $editProdForm = $this->createForm(ProductType::class,$editProduct);
        $editProdForm-> handleRequest($request);
        if($editProdForm->isSubmitted() && $editProdForm->isValid()){
            $slugify= new Slugify();
            $filePng= $editProduct->getPng();
            $filePdf= $editProduct->getPdf();
            $fileImage= $editProduct->getImage();
            $fileNamePng=  $slugify-> slugify($editProduct->getProductName()).'.'.$filePng->guessExtension();
            $fileNamePdf=  $slugify-> slugify($editProduct->getProductName()).'.'.$filePdf->guessExtension();
            $fileNameImage=  $slugify-> slugify($editProduct->getProductName()).'g.'.$fileImage->guessExtension();
            if($filePng->guessExtension()!='png'||$fileImage->guessExtension()!='png'){
                $this->addFlash(
                    'danger',
                    "votre image doit être en format png "
                );  
            }elseif($filePdf->guessExtension()!='pdf')
            {
                $this->addFlash(
                    'danger',
                    "votre fichier doit être en format pdf "
                );  
            }else{
                $filePng->move($this->getParameter('upload_directory_png'),$fileNamePng);
                $fileImage->move($this->getParameter('upload_directory_png'),$fileNameImage);
                $filePdf->move($this->getParameter('upload_directory_pdf'),$fileNamePdf);
                $editProduct->setPng($fileNamePng);
                $editProduct->setPdf($fileNamePdf);
                $editProduct->setImage($fileNameImage);
                $manager=$this->getDoctrine()->getManager();
                foreach ($editProduct->getCharacteristics() as $chara){
                    $chara->setProduct($editProduct);
                    $manager->persist($chara);
                }
                foreach ($editProduct->getJobProducts() as $jp){
                    $jp->setProduct($editProduct);
                    $manager->persist($jp);
                }
                $manager->persist($editProduct); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Le produit ".$editProduct->getProductName()." a bien été modifié "
            );
            return $this-> redirectToRoute('products');
            } 
        }
        return $this->render('dashbord/editProduct.html.twig', [
            'categorys'=> $categorys,//drop-down nos produits
            'editProdForm'=>$editProdForm->createView(),
        ]);
    }

    /**
     * permet de supprimer un produit
     * @Route("/dashbord/supprimer-produit/{image} ", name="removeProduct")
     * @return Response
     */
    public function removeProduct($image,ProductRepository $productRepo)
    {   
        $removeProduct = $productRepo->findOneById($image);
        $filePng= $removeProduct->getPng();
        $fileImage= $removeProduct->getImage();
        $filePdf= $removeProduct->getPdf();
        unlink('../public/images/'.$filePng);
        unlink('../public/images/'.$fileImage);
        unlink('../public/fiches-technique/'.$filePdf);
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeProduct); 
        $manager->flush();
        $this->addFlash(
            'success',
            "Le produit ".$removeProduct->getProductName()." a bien été supprimé "
        );
        return $this-> redirectToRoute('products');
        
        return $this->render('dashbord/index.html.twig', [
            
        ]);
    }

/***************************************************************************************************/

    /**
     * @Route("/dashbord/metier", name="job")
     */
    public function showJob(CategoryRepository $categoryRepo,JobRepository $jobRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        return $this->render('/dashbord/showJob.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
        ]);
    }

    /**
     * permet d'ajouter un métier 
     * @Route("/dashbord/ajouter-metier", name="addJob")
     * @return Response
     */
    public function addJob(CategoryRepository $categoryRepo,Request $request)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits

        $addJob = new Job();
        $addJobForm = $this->createForm(JobType::class,$addJob);
        $addJobForm-> handleRequest($request);
        if($addJobForm->isSubmitted() && $addJobForm->isValid())
        {
            $slugify= new Slugify();
            $file= $addJob->getImage();
            $fileName=  $slugify-> slugify($addJob->getJob()).'.'.$file->guessExtension();
            if($file->guessExtension()!='png'){
                $this->addFlash(
                    'danger',
                    "votre image doit être en format png "
                );  
            }else
            {
                    $file->move($this->getParameter('upload_directory_png'),$fileName);
                    $addJob->setImage($fileName);
                    $manager=$this->getDoctrine()->getManager();
                    $manager->persist($addJob); 
                    $manager->flush();
                    $this->addFlash(
                        'success',
                        "Le métier ".$addJob->getJob()." a bien été ajouté "
                );
                return $this-> redirectToRoute('job');
            }
        } 
        
        return $this->render('dashbord/addJob.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'addJobForm'=> $addJobForm->createView(),
        ]);
    }

    /**
     * permet de modifier un métier
     * @Route("/dashbord/modifier-metier/{job} ", name="editJob")
     * @return Response
     */
    public function editJob($job,CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request)
    {   
        $categorys = $categoryRepo->findAll();//drop-down nos produits

        $editJob = $jobRepo->findOneBySlug($job);
        $editJobForm = $this->createForm(JobType::class,$editJob);
        $editJobForm-> handleRequest($request);
        if($editJobForm->isSubmitted() && $editJobForm->isValid())
        {
            $slugify= new Slugify();
            $file= $editJob->getImage();
            $fileName=  $slugify-> slugify($editJob->getJob()).'.'.$file->guessExtension();
            if($file->guessExtension()!='png'){
                $this->addFlash(
                    'danger',
                    "votre image doit être en format png "
                );  
            }else
            {
                $file->move($this->getParameter('upload_directory_png'),$fileName);
                $editJob->setImage($fileName);
                $manager=$this->getDoctrine()->getManager();
                $manager->persist($editJob); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Le métier ".$editJob->getJob()." a bien été modifié "
                );
                return $this-> redirectToRoute('job');
            }
        } 
        
        return $this->render('dashbord/editJob.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'editJobForm'=> $editJobForm->createView(),
        ]);
    }

    /**
     * permet de supprimer un métier
     * @Route("/dashbord/supprimer-metier/{job} ", name="removeJob")
     * @return Response
     */
    public function removeJob($job,CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request)
    {   
        $categorys = $categoryRepo->findAll();//drop-down nos produits

        $removeJob = $jobRepo->findOneBySlug($job);
        $file= $removeJob->getImage();
        unlink('../public/images/'.$file);
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeJob); 
        $manager->flush();
            $this->addFlash(
                'success',
                "Le métier ".$removeJob->getJob()." a bien été supprimé "
            );
            return $this-> redirectToRoute('job');
         
        
        return $this->render('dashbord/editJob.html.twig', [
            
        ]);
    }

/***************************************************************************************************/






















}
