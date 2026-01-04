<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Movie;
use App\Entity\Screening;
use App\Repository\MovieRepository;
use App\Repository\ScreeningRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


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
    public function show(Movie $movie, ScreeningRepository $screeningRepository): Response
    {
        // Récupérer les séances liées au film
        $screenings = $screeningRepository->findScreeningsFromMovie($movie);

        return $this->render('show.html.twig', [
            'movie' => $movie,
            'screenings' => $screenings,
        ]);
    }


    #[Route('/booking', name: 'app_booking')]
    public function index(
        Request $request,
        MovieRepository $movieRepository,
        ScreeningRepository $screeningRepository
    ): Response {
        // Film pré-sélectionné (si on vient de /movies/{id})
        $movieId = $request->query->get('movie');
        $movie = $movieId ? $movieRepository->find($movieId) : null;

        // Séances liées au film
        $screenings = $movie
            ? $screeningRepository->findBy(['movie' => $movie])
            : [];

        return $this->render('booking.html.twig', [
            'movie' => $movie,
            'movies' => $movieRepository->findAll(),
            'screenings' => $screenings,
        ]);
    }
    
    #[Route('/booking/recap', name: 'app_booking_recap', methods: ['POST'])]
    public function recap(
        Request $request,
        ScreeningRepository $screeningRepository,
        EntityManagerInterface $em
    ): Response
    {
        // Récupérer les données du formulaire
        $screeningId = $request->request->get('screening');
        $places = (int) $request->request->get('places');

        // Récupérer la séance
        $screening = $em->getRepository(Screening::class)->find($screeningId);

        // Vérifier si la réservation est valide
        if (!$screeningRepository->isReservationValid($screening, $places)) {
            $this->addFlash('error', 'Séance invalide ou nombre de places incorrect.');
            return $this->redirectToRoute('app_booking', ['movie' => $screening?->getMovie()?->getId()]);
        }

        // Récupère l'utilisateur connecté
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $userEmail = $user ? $user->getEmail() : 'Invité';

        // Rendu du récapitulatif
        return $this->render('recap.html.twig', [
            'screening' => $screening,
            'places' => $places,
            'pricePerPerson' => $screening->getPrice(),
            'totalPrice' => $screening->getTotalPrice($places),
            'userEmail' => $userEmail,
        ]);
    }


    #[Route('/booking/confirm', name: 'app_booking_confirm', methods: ['POST'])]
    public function confirm(Request $request, EntityManagerInterface $em): Response
    {
        $screeningId = $request->request->get('screening');
        $places = (int) $request->request->get('places');

        $screening = $em->getRepository(Screening::class)->find($screeningId);
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour réserver.');
            return $this->redirectToRoute('app_login');
        }

        if (!$screening || $places < 1 || $places > $screening->getRemainingPlaces()) {
            $this->addFlash('error', 'Séance invalide ou nombre de places incorrect.');
            return $this->redirectToRoute('app_booking', ['movie' => $screening?->getMovie()?->getId()]);
        }

        // Création de la réservation
        $booking = new Booking();
        $booking->setUser($user)
                ->setScreening($screening)
                ->setPlaces($places)
                ->setPricePerPerson(10) // à adapter
                ->setDate(new \DateTime());

        $em->persist($booking);


        $em->flush();

        return $this->redirectToRoute('app_booking_success');
    }

    #[Route('/booking/success', name: 'app_booking_success')]
    public function success(): Response
    {
        return $this->render('success.html.twig');
    
    }
}
