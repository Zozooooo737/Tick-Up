<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Entity\Movie;
use App\Entity\Room;
use App\Entity\Screening;
use App\Entity\Booking;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('easyadmin.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle("Tick'Up");
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Movies', 'fa fa-film', Movie::class);
        yield MenuItem::linkToCrud('Rooms', 'fa fa-door-open', Room::class);
        yield MenuItem::linkToCrud('Screenings', 'fa fa-calendar-alt', Screening::class);
        yield MenuItem::linkToCrud('Bookings', 'fa fa-ticket-alt', Booking::class);
    }
}
