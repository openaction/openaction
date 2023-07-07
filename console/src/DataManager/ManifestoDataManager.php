<?php

namespace App\DataManager;

use App\Cdn\CdnUploader;
use App\Entity\Project;
use App\Entity\Website\ManifestoTopic;
use Doctrine\ORM\EntityManagerInterface;

class ManifestoDataManager
{
    private CdnUploader $uploader;
    private EntityManagerInterface $em;

    public function __construct(CdnUploader $u, EntityManagerInterface $em)
    {
        $this->uploader = $u;
        $this->em = $em;
    }

    public function duplicate(ManifestoTopic $topic, bool $keepPublishStatus = false): ManifestoTopic
    {
        // Duplicate topic
        $duplicateTopic = $topic->duplicate();

        if ($topic->getImage()) {
            $duplicateTopic->setImage($this->uploader->duplicate($topic->getImage()));
        }

        if ($keepPublishStatus) {
            $duplicateTopic->setPublishedAt($topic->getPublishedAt());
        }

        $this->em->persist($duplicateTopic);
        $this->em->flush();

        // Duplicate proposals
        foreach ($topic->getProposals() as $proposal) {
            $duplicateProposal = $proposal->duplicate();
            $duplicateProposal->setTopic($duplicateTopic);

            $this->em->persist($duplicateProposal);
        }

        $this->em->flush();

        return $duplicateTopic;
    }

    public function move(ManifestoTopic $topic, Project $intoProject): ManifestoTopic
    {
        if ($topic->getProject()->getId() === $intoProject->getId()) {
            return $topic;
        }

        $topic->setProject($intoProject);
        $this->em->persist($topic);
        $this->em->flush();

        return $topic;
    }
}
