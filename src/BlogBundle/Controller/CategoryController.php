<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Category;
use BlogBundle\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class CategoryController extends Controller {

    public function __construct() {
        $this->session = new Session();
    }

    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $category_repo = $em->getRepository("BlogBundle:Category");
        $categories = $category_repo->findAll();
        return $this->render("BlogBundle:Category:index.html.twig", array(
            "categories" => $categories
        ));
    }

    public function addAction(Request $request) {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $category = new Category();
                $category->setName($form->get("name")->getData());
                $category->setDescription($form->get("description")->getData());
                $em->persist($category);
                $flush = $em->flush();

                if ($flush == null) {
                    $message = "Category was created successfully";
                    $status = 0;
                } else {
                    $message = "Category creation failed";
                    $status = 1;
                }
            } else {
                $status = 1;
                $message = "Category was not created, data is not valid";
            }
            $this->session->getFlashBag()->add("message", $message);
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("blog_index_category");
        }


        return $this->render("BlogBundle:Category:add.html.twig", array(
            "form" => $form->createView()
        ));
    }

    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $category_repo = $em->getRepository("BlogBundle:Category");
        $category = $category_repo->find($id);
        if (count($category->getEntries()) == 0) {
            $em->remove($category);
            $em->flush();
        }
        return $this->redirectToRoute("blog_index_category");
    }

    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $category_repo = $em->getRepository("BlogBundle:Category");
        $category = $category_repo->find($id);

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $category->setName($form->get("name")->getData());
                $category->setDescription($form->get("description")->getData());
                $em->persist($category);
                $flush = $em->flush();

                if ($flush == null) {
                    $message = "Category was edited successfully";
                    $status = 0;
                } else {
                    $message = "Category edition failed";
                    $status = 1;
                }
            } else {
                $status = 1;
                $message = "Category was not edited, data is not valid";
            }
            $this->session->getFlashBag()->add("message", $message);
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("blog_index_category");
        }

        return $this->render("BlogBundle:Category:edit.html.twig", array(
            "form" => $form->createView()
        ));
    }
}
