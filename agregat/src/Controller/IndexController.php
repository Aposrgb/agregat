<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/{a}', name: 'front', methods: ['GET'])]
    public function front(Request $request): Response
    {
        return $this->render('index.html');
    }

    #[Route('/{a}/{b}', name: 'front2', methods: ['GET'])]
    public function front2(Request $request): Response
    {
        return $this->render('index.html');
    }

    #[Route('/{a}/{b}/{c}', name: 'front3', methods: ['GET'])]
    public function front3(Request $request): Response
    {
        return $this->render('index.html');
    }

    #[Route('/{a}/{b}/{c}/{d}', name: 'front4', methods: ['GET'])]
    public function front4(Request $request): Response
    {
        return $this->render('index.html');
    }
}