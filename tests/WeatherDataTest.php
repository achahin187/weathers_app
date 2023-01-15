<?php

namespace App\Tests;

use App\Entity\Weather;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class WeatherDataTest extends TestCase
{
    public function testSomething(): void
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
        $this->assertTrue(true);
    }
}