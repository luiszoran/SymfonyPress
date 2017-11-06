<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        echo "<h2>Entry</h2>";
        $em = $this->getDoctrine()->getManager();
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        $entries = $entry_repo->findAll();

        foreach ($entries as $entry){
            echo $entry->getTitle()."</br>";
            echo $entry->getCategory()->getName()."</br>";
            echo $entry->getUser()->getName()."</br>";

            $tags = $entry->getEntryTag();
            foreach ($tags as $tag){
                echo $tag->getTag()->getName().", ";
            }

            echo "<hr/>";
        }

        echo "<h2>Category</h2>";
        $em = $this->getDoctrine()->getManager();
        $category_repo = $em->getRepository("BlogBundle:Category");
        $categories = $category_repo->findAll();

        foreach ($categories as $category){
            echo $category->getName()."</br>";

            $entries = $category->getEntries();
            foreach ($entries as $entry){
                echo $entry->getTitle().", ";
            }

            echo "<hr/>";
        }

        echo "<h2>Entries</h2>";
        $em = $this->getDoctrine()->getManager();
        $tag_repo = $em->getRepository("BlogBundle:Tag");
        $tags = $tag_repo->findAll();

        foreach ($tags as $tag){
            echo $tag->getName()."</br>";

            $entryTag = $tag->getEntryTag();
            foreach ($entryTag as $entry){
                echo $entry->getEntry()->getTitle().", ";
            }

            echo "<hr/>";
        }

        return $this->render('BlogBundle:Default:index.html.twig');
    }
}
