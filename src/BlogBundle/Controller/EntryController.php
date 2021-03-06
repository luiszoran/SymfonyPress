<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Entry;
use BlogBundle\Form\EntryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class EntryController extends Controller {

    public function __construct() {
        $this->session = new Session();
    }

    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        $entries = $entry_repo->findAll();
        return $this->render("BlogBundle:Entry:index.html.twig", array(
            "entries" => $entries
        ));
    }

    public function addAction(Request $request) {
        $entry = new Entry();
        $form = $this->createForm(EntryType::class, $entry);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $category_repo = $em->getRepository("BlogBundle:Category");
                $entry_repo = $em->getRepository("BlogBundle:Entry");
                $entry = new Entry();
                $entry->setTitle($form->get("title")->getData());
                $entry->setContent($form->get("content")->getData());
                $entry->setStatus($form->get("status")->getData());

                $file = $form->get("image")->getData();
                if (!empty($file) && $file != null) {
                    $ext = $file->guessExtension();
                    $file_name = time() . "." . $ext;
                    $file->move("uploads", $file_name);
                    $entry->setImage($file_name);
                }else{
                    $entry->setImage(null);
                }

                $category = $category_repo->find($form->get("category")->getData());
                $entry->setCategory($category);

                $user = $this->getUser();
                $entry->setUser($user);

                $em->persist($entry);
                $flush = $em->flush();

                $entry_repo->saveEntryTags($form->get("tags")->getData(),
                    $form->get("title")->getData(),
                    $form->get("category")->getData(),
                    $user,
                    $entry);


                if ($flush == null) {
                    $message = "Entry was created successfully";
                    $status = 0;
                } else {
                    $message = "Entry creation failed";
                    $status = 1;
                }
            } else {
                $status = 1;
                $message = "Entry was not created, data is not valid";
            }
            $this->session->getFlashBag()->add("message", $message);
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("blog_index_entry");
        }


        return $this->render("BlogBundle:Entry:add.html.twig", array(
            "form" => $form->createView()
        ));
    }

    public
    function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        $entry_tag_repo = $em->getRepository("BlogBundle:EntryTag");
        $entry = $entry_repo->find($id);
        $entry_tags = $entry_tag_repo->findBy(array("entry" => $entry));

        foreach ($entry_tags as $entry_tag) {
            $em->remove($entry_tag);
            $em->flush();
        }

        $em->remove($entry);
        $em->flush();

        return $this->redirectToRoute("blog_index_entry");
    }

    public
    function editAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        $entry = $entry_repo->find($id);
        $entry_image = $entry->getImage();

        $tags = "";
        foreach ($entry->getEntryTag() as $entryTag) {
            $tags .= $entryTag->getTag()->getName() . ",";
        }

        $form = $this->createForm(EntryType::class, $entry);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $category_repo = $em->getRepository("BlogBundle:Category");
                $entry->setTitle($form->get("title")->getData());
                $entry->setContent($form->get("content")->getData());
                $entry->setStatus($form->get("status")->getData());

                $file = $form->get("image")->getData();
                if (!empty($file) && $file != null) {
                    $ext = $file->guessExtension();
                    $file_name = time() . "." . $ext;
                    $file->move("uploads", $file_name);
                    $entry->setImage($file_name);
                }else{
                    $entry->setImage($entry_image);
                }

                $category = $category_repo->find($form->get("category")->getData());
                $entry->setCategory($category);

                $user = $this->getUser();
                $entry->setUser($user);

                $em->persist($entry);
                $flush = $em->flush();

                $entry_repo->saveEntryTags($form->get("tags")->getData(),
                    $form->get("title")->getData(),
                    $form->get("category")->getData(),
                    $user,
                    $entry);

                if ($flush == null) {
                    $message = "Entry was edited successfully";
                    $status = 0;
                } else {
                    $message = "Entry edition failed";
                    $status = 1;
                }
            } else {
                $status = 1;
                $message = "Entry was not edited, data is not valid";
            }
            $this->session->getFlashBag()->add("message", $message);
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("blog_index_entry");
        }

        return $this->render("BlogBundle:Entry:edit.html.twig", array(
            "form" => $form->createView(),
            "entry" => $entry,
            "tags" => $tags
        ));
    }
}
