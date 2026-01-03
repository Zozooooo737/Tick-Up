<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Movie;
use App\Entity\Room;
use App\Entity\Screening;
use App\Entity\Booking;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

use Symfony\Component\Yaml\Yaml;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $admin = new User();
        $admin->setEmail('root@gmail.com')
              ->setFirstName('Admin')
              ->setLastName('Master')
              ->setRoles(['ROLE_ADMIN'])
              ->setPassword(password_hash('root', PASSWORD_BCRYPT));

        $manager->persist($admin);


        $users = [];
        // Création de 20 utilisateurs
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName)
                 ->setLastName($faker->lastName)
                 ->setEmail($faker->unique()->email)
                 ->setPassword(password_hash('password', PASSWORD_BCRYPT))
                 ->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $users[] = $user;
        }

        $movies = [];
        // Création de 15 films
        $moviesData = Yaml::parseFile(__DIR__ . '/Data/movies.yaml')['movies'];

        foreach ($moviesData as $data) {
            $movie = new Movie();
            $movie->setTitle($data['title'])
                  ->setSynopsis($data['synopsis'])
                  ->setDuration($data['duration'])
                  ->setGenre($data['genre'])
                  ->setDirector($data['director'])
                  ->setActors($data['actors'])
                  ->setReleaseDate(new \DateTime($data['releaseDate']))
                  ->setPoster($data['poster'])
                  ->setTrailer($data['trailer']);

            $manager->persist($movie);
            $movies[] = $movie;
        }

        $rooms = [];
        // Création de 5 salles
        for ($i = 0; $i < 5; $i++) {
            $room = new Room();
            $room->setName('Salle ' . ($i + 1))
                 ->setCapacity($faker->numberBetween(50, 200));
            $manager->persist($room);
            $rooms[] = $room;
        }

        $screenings = [];
        // Création de 50 séances aléatoires
        for ($i = 0; $i < 50; $i++) {
            $screening = new Screening();
            $screening->setMovie($faker->randomElement($movies))
                      ->setRoom($faker->randomElement($rooms))
                      ->setDateTime($faker->dateTimeBetween('now', '+1 month'));
            $manager->persist($screening);
            $screenings[] = $screening;
        }

        // Création de 100 réservations aléatoires
        for ($i = 0; $i < 100; $i++) {
            $booking = new Booking();
            $screening = $faker->randomElement($screenings);
            $places = $faker->numberBetween(1, 5);
            $pricePerPerson = $faker->randomFloat(2, 5, 20);

            $booking->setUser($faker->randomElement($users))
                    ->setScreening($screening)
                    ->setPlaces($places)
                    ->setPricePerPerson($pricePerPerson)
                    ->setDate($faker->dateTimeBetween('-1 month', 'now'));

            $manager->persist($booking);
        }

        $manager->flush();
    }
}
