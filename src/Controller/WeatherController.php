<?php

namespace App\Controller;

use App\Entity\Weather;
use App\Repository\WeatherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use App\Factory\JsonResponseFactory;


class WeatherController extends AbstractController
{


    private $WeatherRepository;
    private $entityManager;


    public function __construct(
        WeatherRepository $WeatherRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->WeatherRepository = $WeatherRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/weather', name: 'app_weather')]
    public function index(): Response
    {
        return $this->render('weather/index.html.twig', [
            'controller_name' => 'WeatherController',
        ]);
    }


    #[Route('/api/get_weather_data', name: 'get_weather_data')]
    public function save_data(ManagerRegistry $doctrine): Response
    {
        ///api for get weather data
        $client = new Client();
        $base_url = 'http://api.weatherstack.com/current?access_key=fee8a8ba1f824b44717e98cfa1ff3402&query=New York';

        $response = $client->get($base_url,  []);
        $body = json_decode($response->getBody(), true);


        $entityManager = $doctrine->getManager();
        $data = json_encode($body);

        ///save data in database
        $Weather = new Weather();
        $Weather->setData([$data]);

        $entityManager->persist($Weather);
        $entityManager->flush();

        ///return  success message after stored data  successful
        return $this->json([
            'message' => 'saved successfully',
        ]);
    }


    #[Route('/api/get_weather_today/{id}', name: 'get_weather_today')]
    public function get_weather_today(Request $request, $id): Response
    {

        $Weather = $this->WeatherRepository->find($id);

        return $this->json($Weather, 200);
    }
}