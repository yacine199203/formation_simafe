<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\JobRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(CategoryRepository $categoryRepo,JobRepository $jobRepo,Request $request,\Swift_Mailer $mailer)
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        $jobs = $jobRepo->findAll();
        $cont = new Contact();
        $form = $this->createForm(ContactType::class,$cont);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            // On crée le message
            $file= $form->get('file')->getData();
            $fileName= 'contactFile.'.$file->guessExtension();
            $file->move($this->getParameter('upload_directory_png'),$fileName);
            $message = (new \Swift_Message('Nouveau contact'))
                // On attribue l'expéditeur
                ->setFrom($form->get('email')->getData())
                // On attribue le destinataire
                ->setTo('bouadjenek.yacin@gmail.com')
                // On crée le texte avec la vue
                ->setBody(
                    $this->renderView(
                        'contact/mail.html.twig', compact('contact')
                    ),
                    'text/html'
                )
                
                ->attach(\Swift_Attachment::fromPath($this->getParameter('upload_directory_png').'/'.$fileName))

            ;
            $mailer->send($message);

            $message1 = (new \Swift_Message('Accusé de récéption'))
            // On attribue l'expéditeur
            ->setFrom('bouadjenek.yacin@gmail.com')
            // On attribue le destinataire
            ->setTo($form->get('email')->getData())
            // On crée le texte avec la vue
            ->setBody(
                $this->renderView(
                    'contact/mailTo.html.twig', compact('contact')
                ),
                'text/html'
            )
        ;
        $mailer->send($message1);

            $this->addFlash(
                'success',
                "Votre message a été transmis, nous vous répondrons dans les meilleurs délais."
            );
            return $this-> redirectToRoute('homePage');
            
        }
        return $this->render('contact/index.html.twig',[
            'contactForm' => $form->createView(),
            'categorys' => $categorys, //drop-down nos produits
            'jobs' => $jobs,
            ]);
    }

}
