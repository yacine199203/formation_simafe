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
use App\Entity\ProductionJob;
use App\Entity\ProductionImage;
use App\Form\ProductionJobType;
use App\Repository\JobRepository;
use App\Repository\ProductRepository;
use App\Repository\SlidersRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductionJobRepository;
use App\Repository\ProductionImageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashbordController extends AbstractController
{

    /**
     * @Route("/dashbord", name="dashbord")
     */
    public function index(CategoryRepository $categoryRepo,JobRepository $jobRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        return $this->render('/dashbord/index.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
        ]);
    }

/***************************************************************************************************/

    /**
     * @Route("/dashbord/categorie", name="category")
     */
    public function showCategory(CategoryRepository $categoryRepo,JobRepository $jobRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        return $this->render('/dashbord/showCategory.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
        ]);
    }

    /**
     * permet d'ajouter une catégorie 
     * @Route("/dashbord/ajouter-categorie", name="addCategory")
     * @return Response
     */
    public function addCategory(CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $addCategory = new Category();
        $addCatForm = $this->createForm(CategoryType::class,$addCategory);
        $addCatForm-> handleRequest($request);
        if($addCatForm->isSubmitted() && $addCatForm->isValid())
        {
            $file= $addCatForm->get('image')->getData();
            if($file != null)
            {
                $fileName=  uniqid().'.'.$file->guessExtension();
                if($file->guessExtension()!='png'){
                    $this->addFlash(
                        'danger',
                        "votre image doit être en format png "
                    ); 
                }else
                {
                    $file->move($this->getParameter('upload_directory_png'),$fileName);
                    $addCategory->setImage($fileName);
                }
            }
            $manager=$this->getDoctrine()->getManager();
            $manager->persist($addCategory); 
            $manager->flush();
            $this->addFlash(
                'success',
                "La catégorie ".$addCategory->getCategoryName()." a bien été ajoutée "
            );
            return $this-> redirectToRoute('category');
            
        }
        
        return $this->render('dashbord/addCategory.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'addCatForm'=> $addCatForm->createView(),
        ]);
    }

    /**
     * permet de modifier une catégorie
     * @Route("/dashbord/modifier-categorie/{categoryName} ", name="editCategory")
     * @return Response
     */
    public function editCategory($categoryName,CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request)
    {   
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $editCategory = $categoryRepo->findOneBySlug($categoryName);
        $editCatForm = $this->createForm(CategoryType::class,$editCategory);
        $editCatForm-> handleRequest($request);
        if($editCatForm->isSubmitted() && $editCatForm->isValid())
        {
            $file= $editCatForm->get('image')->getData();
            if($file != null)
            {
                unlink('../public/images/'.$file);
                $fileName=  uniqid().'.'.$file->guessExtension();
                if($file->guessExtension()!='png'){
                    $this->addFlash(
                        'danger',
                        "votre image doit être en format png "
                    ); 
                }else
                {
                    $file->move($this->getParameter('upload_directory_png'),$fileName);
                    $editCategory->setImage($fileName);
                }
            }
            $manager=$this->getDoctrine()->getManager();
            $manager->persist($editCategory); 
            $manager->flush();
            $this->addFlash(
                'success',
                "La catégorie ".$editCategory->getCategoryName()." a bien été modifiée "
            );
            return $this-> redirectToRoute('category');
            
        }
        return $this->render('dashbord/editCategory.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'editCatForm'=> $editCatForm->createView(),
        ]);
    }

    /**
     * permet de supprimer une catégorie
     * @Route("/dashbord/supprimer-categorie/{categoryName} ", name="removeCategory")
     * @return Response
     */
    public function removeCategory($categoryName,CategoryRepository $categoryRepo,ProductRepository $productRepo)
    {   
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $products = $productRepo->findByCategory($categoryName);
        $removeCategory = $categoryRepo->findOneBySlug($categoryName);
        $file= $removeCategory->getImage();
        if($removeCategory->getImage() != null){
            unlink('../public/images/'.$file);
        }
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
    public function showSliders(CategoryRepository $categoryRepo,JobRepository $jobRepo,SlidersRepository $slidersRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $sliders = $slidersRepo->findAll();
        return $this->render('/dashbord/showSliders.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'sliders' => $sliders,
        ]);
    }


    /**
     * permet d'ajouter un slider
     * @Route("/dashbord/ajouter-slid", name="addSlid")
     * @return Response
     */
    public function addSlid(CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $addSlider = new Sliders();
        $addSlidForm = $this->createForm(SlidersType::class,$addSlider);
        $addSlidForm-> handleRequest($request);
        if($addSlidForm->isSubmitted() && $addSlidForm->isValid())
        {
            
            $file= $addSlidForm->get('image')->getData();
            if($file == null)
            {
                $this->addFlash(
                    'danger',
                    "Le champ Image est vide"
                );
            }else
            {
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
        }
        return $this->render('dashbord/addSlid.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
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
    public function showProducts(CategoryRepository $categoryRepo,JobRepository $jobRepo,ProductRepository $productRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $products = $productRepo->findAll();
        return $this->render('/dashbord/showProduct.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'products' => $products,
        ]);
    }

    /**
      * permet d'ajouter un produit
     * @Route("/dashbord/ajouter-produit", name="addProduct")
     * @return Response
     */
    public function addProduct(CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();//pour bloquer l'ajout d'une relation métier/ produit si la table métier est vide 
        $addProduct = new Product();
        $addProdForm = $this->createForm(ProductType::class,$addProduct);
        $addProdForm-> handleRequest($request);
        if($addProdForm->isSubmitted() && $addProdForm->isValid())
        {
            
            $filePng= $addProdForm->get('png')->getData();
            $filePdf= $addProdForm->get('pdf')->getData();
            $fileImage= $addProdForm->get('image')->getData();
            if($filePng == null)
            {
                $this->addFlash(
                    'danger',
                    "Le champ Image de présentation est vide \n"
                );
            }
            elseif($filePdf == null)
            {
                $this->addFlash(
                    'danger',
                    "Le champ Fiche téchnique est vide \n"
                );
            }
            elseif($fileImage == null)
            {
                $this->addFlash(
                    'danger',
                    "Le champ Image est vide \n"
                );
            }
            else
            {
                $fileNamePng=  uniqid().'.'.$filePng->guessExtension();
                $fileNamePdf=  uniqid().'.'.$filePdf->guessExtension();
                $fileNameImage=  uniqid().'.'.$fileImage->guessExtension();
                if($filePng->guessExtension()!='png' || $fileImage->guessExtension()!='png' )
                {
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
                }
                $filePng->move($this->getParameter('upload_directory_png'),$fileNamePng);
                $filePdf->move($this->getParameter('upload_directory_pdf'),$fileNamePdf);
                $fileImage->move($this->getParameter('upload_directory_png'),$fileNameImage);
                $addProduct->setPng($fileNamePng);
                $addProduct->setPdf($fileNamePdf);
                $addProduct->setImage($fileNameImage);
            

                $manager=$this->getDoctrine()->getManager();
                foreach ($addProduct->getCharacteristics() as $chara)
                {
                    $chara->setProduct($addProduct);
                    $manager->persist($chara);
                }
                foreach ($addProduct->getJobProducts() as $jp)
                {
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
            'jobs'=> $jobs,//pour bloquer l'ajout d'une relation métier/ produit si la table métier est vide 
            'addProdForm'=> $addProdForm->createView(),
            
        ]);
    }

    /**
     * permet de modifier un produit
     * @Route("/dashbord/modifier-produit/{productName} ", name="editProduct")
     * @return Response
     */
    public function editprod($productName,CategoryRepository $categoryRepo,JobRepository $jobRepo,ProductRepository $productRepo,Request $request)
    {   $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $editProduct = $productRepo->findOneBySlug($productName);
        $editProdForm = $this->createForm(ProductType::class,$editProduct);
        $editProdForm-> handleRequest($request);
        if($editProdForm->isSubmitted() && $editProdForm->isValid())
        {
            $filePng= $editProdForm->get('png')->getData();
            $filePdf= $editProdForm->get('pdf')->getData();
            $fileImage= $editProdForm->get('image')->getData();
            if($filePng != null)
            {
                if($filePng->guessExtension()!='png')
                {
                    $this->addFlash(
                        'danger',
                        "votre image doit être en format png "
                    );
                }else
                {
                    unlink('../public/images/'.$editProduct->getPng());
                    $fileNamePng=  uniqid().'.'.$filePng->guessExtension();
                    $filePng->move($this->getParameter('upload_directory_png'),$fileNamePng);
                    $editProduct->setPng($fileNamePng);
                }    
            }if($filePdf != null)
            {
                if($filePdf->guessExtension()!='pdf')
                {
                    $this->addFlash(
                        'danger',
                        "votre fichier doit être en format pdf "
                    );   
                }else
                {
                    unlink('../public/fiches-technique/'.$editProduct->getPdf());
                    $fileNamePdf=  uniqid().'.'.$filePdf->guessExtension();
                    $filePdf->move($this->getParameter('upload_directory_pdf'),$fileNamePdf);
                    $editProduct->setPdf($fileNamePdf);
                }
            }if($fileImage != null)
            {
                if($fileImage->guessExtension()!='png' )
                {
                    $this->addFlash(
                        'danger',
                        "votre image doit être en format png "
                    ); 
                }else
                {
                    unlink('../public/images/'.$editProduct->getImage());
                    $fileNameImage=  uniqid().'.'.$fileImage->guessExtension();
                    $fileImage->move($this->getParameter('upload_directory_png'),$fileNameImage);
                    $editProduct->setImage($fileNameImage);
                }
            }
            

            $manager=$this->getDoctrine()->getManager();
            foreach ($editProduct->getCharacteristics() as $chara)
            {
                $chara->setProduct($editProduct);
                $manager->persist($chara);
            }
            foreach ($editProduct->getJobProducts() as $jp)
            {
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
        return $this->render('dashbord/editProduct.html.twig', [
            'categorys'=> $categorys,//drop-down nos produits
            'jobs'=> $jobs,//pour bloquer l'ajout d'une relation métier/ produit si la table métier est vide 
            'editProdForm'=> $editProdForm->createView(),
            
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
        if($removeProduct->getPng() != null){
            unlink('../public/images/'.$removeProduct->getPng());
            
        }
        if($removeProduct->getPdf() != null){
            unlink('../public/fiches-technique/'.$removeProduct->getPdf());
        }
        if($removeProduct->getImage() != null){
            unlink('../public/images/'.$removeProduct->getImage());
        }
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
    public function addJob(CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $addJob = new Job();
        $addJobForm = $this->createForm(JobType::class,$addJob);
        $addJobForm-> handleRequest($request);
        if($addJobForm->isSubmitted() && $addJobForm->isValid())
        {
            $file= $addJobForm->get('image')->getData();
            if($file == null)
            {
                $this->addFlash(
                    'danger',
                    "Le champ Image est vide"
                ); 
            }else
            {
                    $fileName=  uniqid().'.'.$file->guessExtension();
                    if($file->guessExtension()!='png'){
                        $this->addFlash(
                            'danger',
                            "votre image doit être en format png "
                        ); 
                    }else
                    {
                        $file->move($this->getParameter('upload_directory_png'),$fileName);
                        $addJob->setImage($fileName);
                    }
                
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
            'jobs' => $jobs,
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
        $jobs = $jobRepo->findAll();
        $editJob = $jobRepo->findOneBySlug($job);
        $editJobForm = $this->createForm(JobType::class,$editJob);
        $editJobForm-> handleRequest($request);
        if($editJobForm->isSubmitted() && $editJobForm->isValid())
        {
            $file= $editJobForm->get('image')->getData();
            if($file != null)
            {
                unlink('../public/images/'.$editJob->getImage());
                $fileName=  uniqid().'.'.$file->guessExtension();
                if($file->guessExtension()!='png')
                {
                    $this->addFlash(
                        'danger',
                        "votre image doit être en format png "
                    ); 
                }else
                    {
                        $file->move($this->getParameter('upload_directory_png'),$fileName);
                        $editJob->setImage($fileName);
                    }           
            }
                $manager=$this->getDoctrine()->getManager();
                $manager->persist($editJob); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Le métier ".$editJob->getJob()." a bien été modifié "
                );
                return $this-> redirectToRoute('job');
                
        }
    
        
        return $this->render('dashbord/editJob.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
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

    /**
     * @Route("/realisation ", name="productionJob")
     */
    public function showProductionJob(CategoryRepository $categoryRepo,JobRepository $jobRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();

        return $this->render('/dashbord/showProductionJob.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
        ]);
    }


    /**
     * permet d'ajouter une réalisation
     * @Route("/dashbord/ajouter-realisation", name="addProductionJob")
     * @return Response
     */
    public function addProductionJob(CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $addProductionJob = new ProductionJob();
        $addProductionJobForm = $this->createForm(ProductionJobType::class,$addProductionJob);
        $addProductionJobForm-> handleRequest($request);
        if($addProductionJobForm->isSubmitted() && $addProductionJobForm->isValid())
        {
            $imgs= $addProductionJobForm->get('image')->getData();
            if($imgs == null)
            {
                $this->addFlash(
                    'danger',
                    "Le champ Image est vide "
                ); 
            }else
            {
                foreach($imgs as $img){
                    $fileName=  uniqid().'.'.$img->guessExtension();
                    if($img->guessExtension()!='png'){
                        $this->addFlash(
                            'danger',
                            "votre image doit être en format png "
                        ); 
                    }else
                    {
                        $img->move($this->getParameter('upload_directory_png'),$fileName);
                        $pi= new ProductionImage();
                        $pi->setImage($fileName);
                        $addProductionJob->addProductionImage($pi);
                    }
                }
                $manager=$this->getDoctrine()->getManager();
                $manager->persist($addProductionJob); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    'L\'album photo de '.$addProductionJob->getCustomer()." a bien été ajouté à ".$addProductionJob->getJob()
                );
                    return $this-> redirectToRoute('productionJob');
            }
        } 
        
        return $this->render('dashbord/addProductionJob.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'addProductionJobForm'=> $addProductionJobForm->createView(),
        ]);
    }


    /**
     * permet de modifier une réalisation
     * @Route("/dashbord/modifier-realisation/{slug}", name="editProductionJob")
     * @return Response
     */
    public function editProductionJob($slug,CategoryRepository $categoryRepo,JobRepository $jobRepo,ProductionJobRepository $productionJobRepo,Request $request)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $editProductionJob = $productionJobRepo->findOneBySlug($slug);
        $editProductionJobForm = $this->createForm(ProductionJobType::class,$editProductionJob);
        $editProductionJobForm-> handleRequest($request);
        if($editProductionJobForm->isSubmitted() && $editProductionJobForm->isValid())
        {
            $imgs= $editProductionJobForm->get('image')->getData();
            foreach($imgs as $img){
                $fileName=  uniqid().'.'.$img->guessExtension();
                if($img->guessExtension()!='png'){
                    $this->addFlash(
                        'danger',
                        "votre image doit être en format png "
                    ); 
                }else
                {
                    $img->move($this->getParameter('upload_directory_png'),$fileName);
                    $pi= new ProductionImage();
                    $pi->setImage($fileName);
                    $editProductionJob->addProductionImage($pi);
                }
            }
            $manager=$this->getDoctrine()->getManager();
            $manager->persist($editProductionJob); 
            $manager->flush();
            $this->addFlash(
                'success',
                'L\'album photo de '.$editProductionJob->getCustomer()." a été modifié à ".$editProductionJob->getJob()
            );
                return $this-> redirectToRoute('productionJob');
        } 
        
        return $this->render('dashbord/editProductionJob.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'editProductionJob' => $editProductionJob,
            'editProductionJobForm'=> $editProductionJobForm->createView(),
        ]);
    }

   

    /**
     * @Route("/supprime/image/{id}", name="annonces_delete_image", methods={"DELETE"})
     */
    public function deleteImage(ProductionImage $image, Request $request){
        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
            // On récupère le nom de l'image
            $nom = $image->getImage();
            // On supprime le fichier
            unlink($this->getParameter('upload_directory_png').'/'.$nom);

            // On supprime l'entrée de la base
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

    /**
     * permet de supprimer une catégorie
     * @Route("/dashbord/supprimer-Pj/{id} ", name="removeProductionJob")
     * @return Response
     */
    public function removeProductionJob($id,ProductionJobRepository $productionJobRepo)
    {   
        $removePj = $productionJobRepo->findOneById($id);
        $rpjs= $removePj->getProductionImages();
        foreach ($rpjs as $rpj){
            unlink('../public/images/'.$rpj->getImage());
        }
        
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removePj); 
        $manager->flush();
            $this->addFlash(
                'success',
                "L'album photo ".$removePj->getCustomer()." a bien été supprimé"
            );
            return $this-> redirectToRoute('productionJob');
        return $this->render('dashbord/showProductionJob.html.twig', [
            
        ]);
    }


















}
