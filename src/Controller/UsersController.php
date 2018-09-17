<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @Route("/users")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/", name="users_index", methods="GET")
     */
    public function index(UsersRepository $usersRepository): Response
    {
        if(isset($_SESSION['user'])&& strlen($_SESSION['user'])>1){
        return $this->render('users/index.html.twig', ['users' => $usersRepository->findAll(),'token'=>'login']);    
        }
        return $this->render('users/index.html.twig', ['users' => $usersRepository->findAll()]);
    }

    /**
     * @Route("/register", name="users_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $user = new Users();
        $form = $this->createFormBuilder($user)
        ->add('Username',TextType::class)
        ->add('Email',EmailType::class)
        ->add('Password',PasswordType::class)
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user=$form->getdata();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('users_index');
        }
        if(isset($_SESSION['user'])&& strlen($_SESSION['user'])>1){
           return $this->render('users/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'token'=>'login'
        ]); 
        }
        return $this->render('users/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="users_login", methods="GET|POST")
     */
    public function login(Request $request): Response
    {
       if(isset($_POST['login'])){
         $user = $this->getDoctrine()
         ->getRepository(Users::class)
         ->findby(['Username' => $_POST['login']]);
         if($user[0]->getUsername() == $_POST['login'] && $user[0]->getPassword() == $_POST['password']){
          if(isset($_SESSION['user'])&& strlen($_SESSION['user'])>1){
              unset($_SESSION['user']);
              unset($_SESSION['id']);
          }
          $_SESSION['user']=$user[0]->getUsername();
          $_SESSION['id']=$user[0]->getId();
          return $this->redirectToRoute('objects_index'); 
      }else {
          $this->addFlash('error','user or password incorrect');
          return $this->redirectToRoute('users_login');
      }

  }
  else 
  {
    if(isset($_SESSION['user'])&& strlen($_SESSION['user'])>1){
        return $this->render('users/login.html.twig',['token'=>'login']);    
        }
    return $this->render('users/login.html.twig');
}
}

    /**
     * @Route("/{id}/edit", name="users_edit", methods="GET|POST")
     */
    public function edit(Request $request, Users $user): Response
    {
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('users_edit', ['id' => $user->getId()]);
        }
         if(isset($_SESSION['user'])&& strlen($_SESSION['user'])>1){
           return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'token'=>'login'
        ]);
        }
        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="users_logout", methods="GET")
     */
    public function logout(): Response
    {
        if(isset($_SESSION['user']) && strlen($_SESSION['user'])>1){
            unset($_SESSION['user']);
            unset($_SESSION['id']);
            return $this->redirectToRoute('objects_index');
        }
        return $this->redirectToRoute('objects_index');
    }
}
