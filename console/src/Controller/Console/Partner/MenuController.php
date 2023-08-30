<?php

namespace App\Controller\Console\Partner;

use App\Controller\AbstractController;
use App\Entity\Model\PartnerMenu;
use App\Form\Partner\Model\PartnerMenuData;
use App\Form\Partner\PartnerMenuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/partner/menu')]
class MenuController extends AbstractController
{
    #[Route('', name: 'console_partner_menu', methods: ['GET', 'POST'])]
    public function menu(EntityManagerInterface $em, Request $request)
    {
        $data = PartnerMenuData::createFromMenu($this->getUser()->getPartnerMenu());

        $form = $this->createForm(PartnerMenuType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getUser()->setPartnerMenu(PartnerMenu::fromArray(['items' => $data->toArray()]));
            $em->persist($this->getUser());
            $em->flush();

            return $this->redirectToRoute('console_partner_menu');
        }

        return $this->renderForm('console/partner/menu.html.twig', [
            'form' => $form,
        ]);
    }
}
