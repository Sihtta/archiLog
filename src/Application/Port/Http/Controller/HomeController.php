<?php

namespace App\Application\Port\Http\Controller;

use App\Repository\CreationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route("/", name: "home.index", methods: ["GET"])]
    public function index(): Response
    {
        return $this->render('pages/home.html.twig');
    }
}
