<?php

namespace App\Controller;

use App\Repository\MaterielRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MaterielController extends AbstractController
{
    #[Route('/', name: 'materiel_search')]
    public function index(MaterielRepository $materielRepository): Response
    {

        
        $materiels_encoded = [];
        $materiels = $materielRepository->findAll();
        
        foreach($materiels as $materiel) {
          array_push($materiels_encoded, $materiel->to_array());
        }

        return $this->render('materiel/search.html.twig', [
            'materiels' => json_encode($materiels_encoded)
        ]);
    }
}


