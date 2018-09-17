<?php

namespace App\Controller;

use App\Entity\Objects;
use App\Entity\Users;
use App\Form\ObjectsType;
use App\Repository\ObjectsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @Route("/")
 */
class ObjectsController extends AbstractController
{
    /**
     * @Route("/", name="objects_index", methods="GET|POST")
     */
    public function index(ObjectsRepository $objectsRepository): Response
    {   
       if(isset($_SESSION['user']) && strlen($_SESSION['user'])>0){
        if(isset($_POST['search']) && strlen($_POST['search'])>0){
            return $this->render('objects/index.html.twig', ['objects'=>$objectsRepository->findby(['Name'=>$_POST['search'],'gave'=>'1']),'token'=>'login','Type'=>$objectsRepository->findby(['Type'=>$_POST['search'],'gave'=>'1'])]);
        }
        return $this->render('objects/index.html.twig', ['objects' => $objectsRepository->findby(['gave'=>'1']),'token'=>'login']);
    }
    if(isset($_POST['search']) && strlen($_POST['search'])>0){
            return $this->render('objects/index.html.twig', ['objects'=>$objectsRepository->findby(['Name'=>$_POST['search']]),'token'=>'login','Type'=>$objectsRepository->findby(['Type'=>$_POST['search']])]);
        }
    return $this->render('objects/index.html.twig', ['objects' => $objectsRepository->findby(['gave'=>'1'])]);
}
    /**
     * @Route("/new", name="objects_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        if(isset($_SESSION['user']) && strlen($_SESSION['user'])>0){
            $user=$this->getDoctrine()->getRepository(Users::class)->find($_SESSION['id']);

            $object = new Objects();
            $form=$this->createFormBuilder($object)
            ->add('Type',TextType::class)
            ->add('Name',TextType::class)
            ->getForm();
            $object->setGave(0);
            $object->setUser($user);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($object);
                $entityManager->flush();
                return $this->redirectToRoute('objects_index');
            }

            if(isset($_SESSION['user']) && strlen($_SESSION['user'])>1){
               return $this->render('objects/new.html.twig', [
                'token'=>'login',
                'form'=>$form->createView()
            ]);
           }
           return $this->render('objects/new.html.twig');
       }
       return $this->redirectToRoute('objects_index');
   }

    /**
     * @Route("/show", name="objects_show", methods="GET")
     */
    public function show(): Response
    {         
        if(isset($_SESSION['user']) && strlen($_SESSION['user'])>0){
            $userobjects = $this->getDoctrine()
            ->getRepository(Objects::class)
            ->findby(['user' => $_SESSION['id'],
                'gave'=>'1'
            ]);
            $notgave=$this->getDoctrine()
            ->getRepository(Objects::class)
            ->findby(['user' => $_SESSION['id'],
                'gave'=>'0'
            ]);
            if ($userobjects!=null || $notgave!=null) {
             return $this->render('objects/show.html.twig', ['objects'=>$userobjects,'token'=>'login','account'=>$notgave]);    
         }
         return $this->render('objects/show.html.twig', ['token'=>'login']);
     }
     return $this->redirectToRoute('objects_index');
 }

    /**
     * @Route("/give/{id}", name="objects_give", methods="GET|POST")
     */
    public function give(Request $request,Objects $object): Response
    {
        if(isset($_SESSION['user']) && strlen($_SESSION['user'])>0){
            $object->setGave(1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();
            return $this->redirectToRoute('objects_index', ['id' => $object->getId()]);
        }
        return $this->redirectToRoute('objects_index');
    }
    /**
     * @Route("/take/{id}", name="objects_take", methods="GET|POST")
     */
    public function take(Request $request,Objects $object): Response
    {
        if(isset($_SESSION['user']) && strlen($_SESSION['user'])>0){
        $user=$this->getDoctrine()->getRepository(Users::class)->find($_SESSION['id']);
        $object->setGave(0);
        $object->setUser($user);
        $em = $this->getDoctrine()->getManager();
        $em->persist($object);
        $em->flush();
        return $this->redirectToRoute('objects_index', ['id' => $object->getId()]);
    }
    return $this->redirectToRoute('objects_index');
    }
}
