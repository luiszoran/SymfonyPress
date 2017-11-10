<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\User;
use BlogBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller {

    public function __construct() {
        $this->session = new Session();
    }

    public function loginAction(Request $request) {
        $authenticationUtils = $this->get("security.authentication_utils");
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // FORM

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $user_repo = $em->getRepository('BlogBundle:User');
                $user = $user_repo->findBy(array("email" => $form->get("email")->getData()));

                if (count($user) == 0) {

                    $user = new User();
                    $user->setName($form->get("name")->getData());
                    $user->setSurname($form->get("surname")->getData());
                    $user->setEmail($form->get("email")->getData());

                    $factory = $this->get("security.encoder_factory");
                    $encoder = $factory->getEncoder($user);
                    $password = $encoder->encodePassword($form->get("password")->getData(), $user->getSalt());

                    $user->setPassword($password);
                    $user->setRole('ROLE_USER');
                    $user->setImagen(null);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $flush = $em->flush();

                    if ($flush == null) {
                        $message = "User registration successful";
                        $status = 0;
                    } else {
                        $message = "User registration failed";
                        $status = 1;
                    }
                } else {
                    $message = "User already exists";
                    $status = 1;
                }

            } else {
                $message = "User registration failed";
                $status = 1;
            }
            // END FORM

            $this->session->getFlashBag()->add("message", $message);
            $this->session->getFlashBag()->add("status", $status);
        }

        return $this->render("BlogBundle:User:login.html.twig", array(
            "error" => $error, "last_username" => $lastUsername, "form" => $form->createView()
        ));
    }
}
