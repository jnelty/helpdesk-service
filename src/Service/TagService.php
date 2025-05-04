<?php

namespace App\Service;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;

class TagService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function getOrCreateManyTags(
        array $tags
    ): array
    {
        $existingTags = $this->entityManager->getRepository(Tag::class)->findAllExistingTags($tags);

        $existingMap = [];
        foreach ($existingTags as $existingTag) {
            $existingMap[$existingTag->getName()] = $existingTag;
        }

        $entityTags = [];
        foreach ($tags as $tag) {
            if (isset($existingMap[$tag])) {
                $entityTags[] = $existingMap[$tag];
            } else {
                $newTag = new Tag();
                $newTag->setName($tag);

                $this->entityManager->persist($newTag);

                $entityTags[] = $newTag;
            }
        }

        return $entityTags;
    }
}