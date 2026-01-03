<?php

namespace App\Controller;

use App\Repository\MovieRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Movie;

final class MyController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function Home(MovieRepository $movieRepository): Response
    {
        $featuredMovie = $movieRepository->findFeaturedMovie();

        return $this->render('base.html.twig', [
            'movie' => $featuredMovie,
        ]);
    }

    #[Route('/movies', name: 'app_movies')]
    public function Movies(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findAll();

        return $this->render('movies.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route('/movies/{id}', name: 'app_show')]
    public function show(Movie $movie): Response
    {
        return $this->render('show.html.twig', [
            'movie' => $movie,
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
