<?php

namespace BlogBundle\Repository;

use BlogBundle\Entity\EntryTag;
use BlogBundle\Entity\Tag;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class EntryRepository extends EntityRepository {

    public function saveEntryTags($tags = null, $title = null, $category = null, $user = null, $entry = null) {
        $em = $this->getEntityManager();
        $tag_repo = $em->getRepository('BlogBundle:Tag');
        $entry_tag_repo = $em->getRepository('BlogBundle:EntryTag');

        if ($entry == null) {
            $entry = $this->findOneBy(array(
                "title" => $title,
                "category" => $category,
                "user" => $user
            ));
        } else {

        }

        $tags = explode(",", $tags);

        foreach ($tags as $tag) {
            $isset_tag = $tag_repo->findOneBy(array("name" => $tag));
            if ($isset_tag === null) {
                $tag_obj = new Tag();
                $tag_obj->setName($tag);
                $em->persist($tag_obj);
                $em->flush();
            }

            $tag = $tag_repo->findOneBy(array("name" => $tag));
            $isset_tag = $entry_tag_repo->findOneBy(array("tag" => $tag->getId()));
            if ($isset_tag === null) {
                $entryTag = new EntryTag();
                $entryTag->setEntry($entry);
                $entryTag->setTag($tag);
                $em->persist($entryTag);
            }

        }
        return $em->flush();
    }

    public function getPaginateEntries($pagesSize=5, $currentPage=1){
        $em = $this->getEntityManager();
        $dql = "SELECT e FROM BlogBundle\Entity\Entry e ORDER BY e.id ASC";
        $query = $em->createQuery($dql)->setFirstResult($pagesSize*($currentPage-1))
            ->setMaxResults($pagesSize);
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        return $paginator;
    }

    public function getCategoryEntries($category, $pagesSize=5, $currentPage=1){
        $em = $this->getEntityManager();
        $dql = "SELECT e FROM BlogBundle\Entity\Entry e WHERE e.category = :category ORDER BY e.id ASC";
        $query = $em->createQuery($dql)->setParameter("category", $category)
            ->setFirstResult($pagesSize*($currentPage-1))
            ->setMaxResults($pagesSize);
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        return $paginator;
    }
}