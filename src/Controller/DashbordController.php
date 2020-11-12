<?php

namespace App\Controller;

use App\Entity\Sliders;
use App\Entity\Category;
use App\Form\SlidersType;
use App\Form\CategoryType;
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
            'editCatForm'=> $editCatForm->createView(),
        ]);
    }

    /**
     * permet de supprimer une catégorie
     * @Route("/dashbord/supprimer-categorie/{categoryName} ", name="removeCategory")
     * @return Response
     */
    public function removeCategory($categoryName,CategoryRepository $categoryRepo,Request $request)
    {   
        $categorys = $categoryRepo->findAll();//drop-down nos produits

        $removeCategory = $categoryRepo->findOneBySlug($categoryName);
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeCategory); 
        $manager->flush();
            $this->addFlash(
                'success',
                "La catégorie ".$removeCategory->getCategoryName()." a bien été supprimée "
            );
            return $this-> redirectToRoute('category');
         
        
        return $this->render('dashbord/editCategory.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
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
            'sliders'=>$sliders
        ]);
    }

/***************************************************************************************************/




















}
