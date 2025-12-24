<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MyController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function Home(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'MyController',
        ]);
    }

    #[Route('/movies', name: 'app_movies')]
    public function Movies(): Response
    {
        return $this->render('movies.html.twig', [
            'controller_name' => 'MyController',
        ]);
    }

    #[Route('/booking', name: 'app_booking')]
    public function Booking(): Response
    {
        return $this->render('booking.html.twig', [
            'controller_name' => 'MyController',
        ]);
    }
    
}
