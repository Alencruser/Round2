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
     * @Route("/", name="objects_index", methods="GET")
     */
    public function index(ObjectsRepository $objectsRepository): Response
    {
     if(isset($_SESSION['user']) && strlen($_SESSION['user'])>1){
        return $this->render('objects/index.html.twig', ['objects' => $objectsRepository->findAll(),'token'=>'login']);
    }
    return $this->render('objects/index.html.twig', ['objects' => $objectsRepository->findAll()]);
}

    /**
     * @Route("/new", name="objects_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $user=$this->getDoctrine()->getRepository(Users::class)->find($_SESSION['id']);

        if (isset($_POST['Object']) || strlen($_POST['Object']>0)) {
            if($user->getObjects()==null){
                $data=$_POST['Object'];
            }else {
                $data=$user->getObjects().",".$_POST['Object'];
            }
            $user->setObjects($data);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('objects_index');
        }

        if(isset($_SESSION['user']) && strlen($_SESSION['user'])>1){
         return $this->render('objects/new.html.twig', [
            'token'=>'login'
        ]);
     }
     return $this->render('objects/new.html.twig');
 }

    /**
     * @Route("/show", name="objects_show", methods="GET")
     */
    public function show(): Response
    {
        if(isset($_SESSION['user']) && strlen($_SESSION['user'])>1){
            $user=$this->getDoctrine()->getRepository(Users::class)->find($_SESSION['id']);
            $notgave=explode(",", $user->getObjects());
            $userobjects = $this->getDoctrine()
            ->getRepository(Objects::class)
            ->findby(['user' => $_SESSION['id']]);
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
    public function give(Request $request): Response
    {
        $word=explode('/',$request->getPathInfo());
        $data=$word[count($word)-1];
        $object= new Objects();
        $form = $this->createFormBuilder($object)
        ->add('Type',TextType::class)
        ->add('Name',TextType::class,array(
            'data'=>$data
        ))
        ->getForm();
        $user=$this->getDoctrine()->getRepository(Users::class)->find($_SESSION['id']);
        $object->setUser($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('objects_index', ['id' => $object->getId()]);
        }
        if(isset($_SESSION['user']) && strlen($_SESSION['user'])>1){
           return $this->render('objects/give.html.twig', [
            'object' => $object,
            'form' => $form->createView(),
            'token'=>'login',
        ]);
       }
       return $this->render('objects/give.html.twig', [
        'object' => $object,
        'form' => $form->createView(),
    ]);
   }

    /**
     * @Route("/{id}", name="objects_delete", methods="DELETE")
     */
    public function delete(Request $request, Objects $object): Response
    {
        if ($this->isCsrfTokenValid('delete'.$object->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($object);
            $em->flush();
        }

        return $this->redirectToRoute('objects_index');
    }
}
