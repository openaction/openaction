<?php

namespace App\DataManager;

use App\Entity\Community\PhoningCampaign;
use Doctrine\ORM\EntityManagerInterface;

class PhoningCampaignDataManager
{
    private FormDataManager $formDataManager;
    private EntityManagerInterface $em;

    public function __construct(FormDataManager $fdm, EntityManagerInterface $em)
    {
        $this->formDataManager = $fdm;
        $this->em = $em;
    }

    public function duplicate(PhoningCampaign $campaign): PhoningCampaign
    {
        $duplicateForm = $this->formDataManager->duplicate($campaign->getForm());

        $duplicate = $campaign->duplicate();
        $duplicate->setForm($duplicateForm);

        $this->em->persist($duplicate);
        $this->em->flush();

        return $duplicate;
    }
}
