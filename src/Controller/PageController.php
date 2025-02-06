<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/page', name: 'page_index')]
public function index(): Response
{
    return $this->render('page/index.html.twig');
}
#[Route('/nav', name: 'ppage_index')]
public function navindex(): Response
{
    return $this->render('page/nav.html.twig');
}
#[Route('/back', name: 'pppage_index')]
public function nabavindex(): Response
{
    return $this->render('page/back.html.twig');
}
}
