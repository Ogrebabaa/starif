<?php

namespace App\Service;

use App\Repository\MaterielRepository;
use App\Repository\MetierRepository;
use App\Repository\TypeRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/** 
 * Sercive use to communicate with Starif's API
 */
class StarifService
{
    private $token;
    private $base_url;
    private $httpClient;

    public function __construct(
        String $token,
        String $base_url,
        HttpClientInterface $httpClient
      )
    {
      $this->token = $token;
      $this->base_url = $base_url;
      $this->httpClient = $httpClient;
    }

    public function getData(string $route, int $page = 1) {
      $response = $this->httpClient->request(
        'GET',
        $this->base_url . $route,
        [
          'query' => [
            'token' => $this->token,
            'page' => $page
          ],
          'extra' => ['trace_content' => false] // disable Profiler logging in dev env to free memory
        ]
      );
      
      return $response->toArray();
    }
    
  }