<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Role;
use App\Entity\User;
use App\Form\JobType;
use App\Form\UserType;
use App\Entity\Product;
use App\Entity\Sliders;
use App\Entity\Category;
use App\Form\ProductType;
use App\Form\SlidersType;
use App\Entity\JobProduct;
use App\Entity\UpdatePass;
use App\Form\CategoryType;
use App\Form\EditUserType;
use Cocur\Slugify\Slugify;
use App\Entity\Recruitement;
use App\Form\UpdatePassType;
use App\Entity\ProductionJob;
use App\Form\EditProductType;
use App\Form\RecruitementType;
use App\Entity\ProductionImage;
use App\Form\ProductionJobType;
use App\Repository\JobRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\SlidersRepository;
use Symfony\Component\Form\FormError;
use App\Repository\CategoryRepository;
use App\Repository\NewsletterRepository;
use App\Repository\RecruitementRepository;
use App\Repository\ProductionJobRepository;
use App\Repository\ProductionImageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DashbordController extends AbstractController
{

    /**
     * @Route("/dashbord", name="dashbord")
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
                $addProdForm->get('png')->addError(new FormError("Ce champ est vide"));
            }
            elseif($filePdf == null)
            {
                $addProdForm->get('pdf')->addError(new FormError("Ce champ est vide"));
            }
            elseif($fileImage == null)
            {
                $addProdForm->get('image')->addError(new FormError("Ce champ est vide"));
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
                    $editUserForm->get('pdf')->addError(new FormError("Le format de ce fichier doit être en PDF"));   
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
               
                foreach($addProdForm->get('jobProducts')->getData() as $jp){
                    $r= new JobProduct();
                    $r->setJob($jp);
                    $addProduct->addJobProduct($r);
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
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function editprod($productName,CategoryRepository $categoryRepo,JobRepository $jobRepo,ProductRepository $productRepo,Request $request)
    {   $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $editProduct = $productRepo->findOneBySlug($productName);
        $editProdForm = $this->createForm(EditProductType::class,$editProduct);
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
                    $editProdForm->get('png')->addError(new FormError("Le format de ce fichier doit être en PNG"));
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
                    $editProdForm->get('pdf')->addError(new FormError("Le format de ce fichier doit être en PDF"));   
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
                    $editProdForm->get('image')->addError(new FormError("Le format de ce fichier doit être en PNG"));
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
                $r= new JobProduct();
                $error=$productRepo->findOneById($editProdForm->get('jobProducts')->getData());
            if( $error==null){
                $editProdForm->get('jobProducts')->addError(new FormError("Ce produit appartient déja à ce métier"));
            }else
            {
                $r->setJob($editProdForm->get('jobProducts')->getData());
                $editProduct->addJobProduct($r);
            
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
            'jobs'=> $jobs,//pour bloquer l'ajout d'une relation métier/ produit si la table métier est vide 
            'editProduct'=> $editProduct,
            'editProdForm'=> $editProdForm->createView(),
            
        ]);
    }

    /**
 * @Route("/supprime/produit/{id}", name="annonces_delete_produit", methods={"DELETE"})
 */
public function deletePro( JobProduct $produit, Request $request){
    $data = json_decode($request->getContent(), true);

    // On vérifie si le token est valide
    if($this->isCsrfTokenValid('delete'.$produit->getId(), $data['_token'])){
     

        // On supprime l'entrée de la base
        $em = $this->getDoctrine()->getManager();
        $em->remove($produit);
        $em->flush();

        // On répond en json
        return new JsonResponse(['success' => 1]);
    }else{
        return new JsonResponse(['error' => 'Token Invalide'], 400);
    }
}

    /**
     * permet de supprimer un produit
     * @Route("/dashbord/supprimer-produit/{image} ", name="removeProduct")
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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

/***************************************************************************************************/

    /**
     * @Route("/dashbord/utilisateur", name="user")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showUser(UserRepository $userRepo,CategoryRepository $categoryRepo,JobRepository $jobRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $users = $userRepo->findAll();
        return $this->render('/dashbord/showUser.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'users' => $users,
        ]);
    }

     /**
     * permet d'ajouter un utilisateur 
     * @Route("/dashbord/ajouter-utilisateur", name="addUser")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function addUser(CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request,UserPasswordEncoderInterface $encoder)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $addUser = new User();
        $addUserForm = $this->createForm(UserType::class,$addUser);
        $addUserForm-> handleRequest($request);
        if($addUserForm->isSubmitted() && $addUserForm->isValid())
        {
            $manager=$this->getDoctrine()->getManager();
            $pass = $encoder->encodePassword($addUser, $addUser->getPass());
           
            $addUser->setPass($pass);
            $manager->persist($addUser); 
            $manager->flush();
            $this->addFlash(
                'success',
                "L'utilisateur ".$addUser->getFirstName()." ".$addUser->getLastName()." a bien été ajouté"
            );
            return $this-> redirectToRoute('user');
        }   
        return $this->render('dashbord/addUser.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'addUserForm'=> $addUserForm->createView(),
        ]);
    }

    /**
     * permet de modifier un utilisateur 
     * @Route("/dashbord/modifier-utilisateur/{id}/{slug}", name="editUser")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function editUser($id,$slug,UserRepository $userRepo, CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $editUser = $userRepo->findOneByid($id);
        $editUserForm = $this->createForm(EditUserType::class,$editUser);
        $editUserForm-> handleRequest($request);
        if($editUserForm->isSubmitted() && $editUserForm->isValid())
        {
            $manager=$this->getDoctrine()->getManager();
            $manager->persist($editUser); 
            $manager->flush();
            $this->addFlash(
                'success',
                "L'utilisateur ".$editUser->getFirstName()." ".$editUser->getLastName()." a bien été modifié"
            );
            return $this-> redirectToRoute('user');
        }  
        return $this->render('dashbord/editUser.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'editUser' => $editUser,
            'editUserForm'=> $editUserForm->createView(),
        ]);
    }

    /**
     * permet de modifier le mot de passe utilisateur 
     * @Route("/dashbord/modifier-mot-de-passe", name="updatePass")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function updatePass(UserRepository $userRepo, CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request,UserPasswordEncoderInterface $encoder)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $user= $this->getUser();
        $updatePass= new UpdatePass();
        $updatePassForm = $this->createForm(UpdatePassType::class,$updatePass);
        $updatePassForm-> handleRequest($request);
        if($updatePassForm->isSubmitted() && $updatePassForm->isValid())
        {
            if (!password_verify($updatePass->getOldPass(), $user->getPass()))
            {
                $this->addFlash(
                    'danger',
                    "Votre ancin mot de passe est incorrect"
                );
            }else
            {
                $newPass= $updatePass->getNewPass();
                $pass= $encoder->encodePassword($user, $newPass);
                $user->setPass($pass);
                $manager=$this->getDoctrine()->getManager();
                $manager->persist($user); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié"
                );
                return $this-> redirectToRoute('user');
            }
            
        }   
        return $this->render('dashbord/updatePass.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'updatePassForm'=> $updatePassForm->createView(),
        ]);
    }


    /**
     * permet de supprimer un utilisateur
     * @Route("/dashbord/supprimer-utilisatuer/{id} ", name="removeUser")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function removeUser($id,UserRepository $userJobRepo)
    {   
        $removeUser = $userJobRepo->findOneById($id);
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeUser); 
        $manager->flush();
            $this->addFlash(
                'success',
                "L'utilisateur ".$removeUser->getFirstName()." ".$removeUser->getLastName()." a bien été supprimé"
            );
            return $this-> redirectToRoute('user');
        return $this->render('dashbord/showProductionJob.html.twig', [
            
        ]);
    }


/***************************************************************************************************/

    /**
     * @Route("/dashbord/newsletter", name="newsletter")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showNewsletter(NewsletterRepository $newsletterRepo,CategoryRepository $categoryRepo,JobRepository $jobRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $subscribers = $newsletterRepo->findAll();
        return $this->render('/dashbord/showNewsletter.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'subscribers' => $subscribers,
        ]);
    }

    /**
     * @Route("/dashbord/newsletter/emails", name="emails")
     * @IsGranted("ROLE_ADMIN")
     */
    public function emails(NewsletterRepository $newsletterRepo): Response
    {
        
        $subscribers = $newsletterRepo->findAll();
        return $this->render('/dashbord/emails.html.twig', [
            'subscribers' => $subscribers,
        ]);
    }

    /**
     * @Route("/dashbord/newsletter/{id}", name="subscriber")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showsubscriber($id,NewsletterRepository $newsletterRepo,CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $subscribers = $newsletterRepo->findOneById($id);
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $manager=$this->getDoctrine()->getManager();
            if($subscribers->getStatus()==false)
            {
                $subscribers->setStatus(true);
                $manager->persist($subscribers); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    $subscribers->getName()." a été validé"
                );
            }else
            {
                $subscribers->setStatus(false);
                $manager->persist($subscribers); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    $subscribers->getName()." a été dévalidé"
                );
            }
           
            return $this-> redirectToRoute('newsletter');
        }   
        return $this->render('/dashbord/showsubscriber.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'subscribers' => $subscribers,
            'form'=> $form->createView(),
        ]);
    }

    /**
     * permet de supprimer un abonné
     * @Route("/dashbord/supprimer-abonne/{id} ", name="removeSubscribers")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function removeSubscribers($id,NewsletterRepository $newsletterRepo)
    {   
        $subscribers = $newsletterRepo->findOneById($id);
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($subscribers); 
        $manager->flush();
            $this->addFlash(
                'success',
                 $subscribers->getName()." a bien été supprimé"
            );
            return $this-> redirectToRoute('newsletter');
        return $this->render('dashbord/showProductionJob.html.twig', [
            
        ]);
    }

    /**
     * permet de supprimer un abonné
     * @Route("/dashbord/supprimer-tous-les-abonnes ", name="removeSubAll")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function removeAllSuscr(NewsletterRepository $newsletterRepo)
    {  
        $subscribers = $newsletterRepo->findAll();
        $manager=$this->getDoctrine()->getManager();
        foreach ($subscribers as $sub)
        {
            $remove= $sub->getUnsubscribe();
            if($remove != false )
            {
                $manager->remove($sub); 
            }
        }
        $manager->flush();
            $this->addFlash(
                'success',
                 "Abonnés supprimés"
            );
            return $this-> redirectToRoute('newsletter');
        return $this->render('dashbord/showProductionJob.html.twig', [
            
        ]);
    }
/***************************************************************************************************/

    /**
     * voir recrutement
     * @Route("/dashbord/recrutement", name="showRecruitment")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showRec(RecruitementRepository $recruitementRepo,CategoryRepository $categoryRepo,JobRepository $jobRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $recruitement = $recruitementRepo->findAll();
        return $this->render('/dashbord/showRecruitement.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'recruitement' => $recruitement,
        ]);
    }

    /**
     * permet d'ajouter une offre d'emploi 
     * @Route("/dashbord/ajouter-offre-d-emploi", name="addRec")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function addRec(CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $addRec = new Recruitement();
        $addRecForm = $this->createForm(RecruitementType::class,$addRec);
        $addRecForm-> handleRequest($request);
        if($addRecForm->isSubmitted() && $addRecForm->isValid())
        {
            $manager=$this->getDoctrine()->getManager();
            foreach ($addRec->getConditions() as $cond)
                {
                    $cond->setRecruitement($addRec);
                    $manager->persist($cond);
                }
            $manager->persist($addRec); 
            $manager->flush();
            $this->addFlash(
                'success',
                "l'offre d'emploi ".$addRec->getJob()." a bien été ajoutée"
            );
            return $this-> redirectToRoute('showRecruitment');
        }   
        return $this->render('dashbord/addRecruitement.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'addRecForm'=> $addRecForm->createView(),
        ]);
    }
     

    /**
     * permet de modifier une offre d'emploi 
     * @Route("/dashbord/modifier-offre-d-emploi/{id}", name="editRec")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function editRec($id,RecruitementRepository $recruitementRepo,CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $editRec = $recruitementRepo->findOneById($id);
        $editRecForm = $this->createForm(RecruitementType::class,$editRec);
        $editRecForm-> handleRequest($request);
        if($editRecForm->isSubmitted() && $editRecForm->isValid())
        {
            $manager=$this->getDoctrine()->getManager();
            foreach ($editRec->getConditions() as $cond)
                {
                    $cond->setRecruitement($editRec);
                    $manager->persist($cond);
                }
            $manager->persist($editRec); 
            $manager->flush();
            $this->addFlash(
                'success',
                "L'offre d'emploi ".$editRec->getJob()." a bien été modifiée"
            );
            return $this-> redirectToRoute('showRecruitment');
        }   
        return $this->render('dashbord/editRecruitement.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            'editRecForm'=> $editRecForm->createView(),
        ]);
    }

    /**
     * permet de supprimer une offre d'emploi
     * @Route("/dashbord/supprimer-offre-d-emploi/{id} ", name="removeRec")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function removeRec($id,RecruitementRepository $recruitementRepo)
    {   
        $removeRec = $recruitementRepo->findOneById($id);
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeRec); 
        $manager->flush();
            $this->addFlash(
                'success',
                 "L'offre d'emploi ".$removeRec->getJob()." a bien été supprimée"
            );
            return $this-> redirectToRoute('showRecruitment');
        return $this->render('dashbord/showProductionJob.html.twig', [
            
        ]);
    }




















}
