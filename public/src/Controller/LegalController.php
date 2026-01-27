<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class LegalController extends AbstractController
{
    #[Route('/legal', name: 'legalities')]
    public function legalities()
    {
        return $this->render('legal/legalities.html.twig');
    }
}
