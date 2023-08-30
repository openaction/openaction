<?php

namespace App\Controller;

use App\Form\Model\SubscribeNewsletterData;
use App\Form\SubscribeNewsletterType;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("", name="homepage")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'newsletter_form' => $this->createForm(SubscribeNewsletterType::class, new SubscribeNewsletterData())->createView(),
        ]);
    }
}
