<?php

namespace App\Command;

use App\Entity\Materiel;
use App\Entity\Metier;
use App\Entity\Type;
use App\Repository\MetierRepository;
use App\Repository\TypeRepository;
use App\Service\StarifService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:importData',
    description: "import data from Starif's API",
)]
class ImportDataCommand extends Command
{

    private $entityManager;
    private $metierRepository;
    private $typeRepository;
    private $starifService;

    public function __construct(
      EntityManagerInterface $entityManager,
      MetierRepository $metierRepository,
      TypeRepository $typeRepository,
      StarifService $starifService
    )
    {
        $this->entityManager = $entityManager;
        $this->metierRepository = $metierRepository;
        $this->typeRepository = $typeRepository;
        $this->starifService = $starifService;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityToImport = ['metiers', 'types', 'materiels'];
        $io = new SymfonyStyle($input, $output);

        foreach($entityToImport as $entity) {
          $response = $this->getDataFromService($entity);
          $response 
            ? $io->success('Data has been successfully imported, enjoy !') 
            : $io->error("Oops, something went wrong ! - $response ");
          
        }
        
        return Command::SUCCESS;
    }

    private function getDataFromService(string $entity) {
      try {
        $data = $this->starifService->getData($entity);
        $nextPage = 2;
        $lastPage = $data["last_page"];
        $this->insertData($data["data"], $entity);

        while($nextPage <= $lastPage) {
          $data = $this->starifService->getData($entity,$nextPage);
          $nextPage = $data["current_page"] + 1;
          $this->insertData($data["data"], $entity);
        }

        return true;
      } catch (Exception $e) {
        return $e;
      } 
    }

    // Convert data into entities and insert in database
    private function insertData(Array $data, string $entity) {
      switch ($entity) {
        case "metiers": 
          foreach($data as $starif_metier) {
            $metier = new Metier();
            $metier->setStarifId($starif_metier["metier_id"]);
            $metier->setNom($starif_metier["nom"]);
            $this->entityManager->persist($metier);
          }
          $this->entityManager->flush();
          break;
        case "types":
          foreach($data as $starif_type) {
            $type = new Type();
            $type->setStarifId($starif_type["type_id"]);
            $type->setNom($starif_type["nom"]);
            $type->setFamille($starif_type["famille"]);
            $metier = $this->metierRepository->findOneBy(["starif_id" => $starif_type["metier_id"]]);
            if ($metier instanceof Metier) {
              $type->setMetier($metier);
            }
            $this->entityManager->persist($type);
          }
          $this->entityManager->flush();
          break;
        case "materiels":
          foreach($data as $starif_materiel) {
            if (sizeof($starif_materiel["tarifs"]) > 0 ) {
              $tarifs = $starif_materiel["tarifs"];
              foreach($tarifs as $tarif) {
                if ($tarif["catalogue"]["catalogue_id"] == 1469) {  // filtre sur catalogue beproactiv
                  $materiel = new Materiel();
                  $materiel->setStarifId($starif_materiel["materiel_id"]);
                  $materiel->setNom($starif_materiel["nom"]);
                  $materiel->setNomCourt($starif_materiel["nom_court"]);
                  $materiel->setMarque($starif_materiel["marque"]);
                  $materiel->setPrixPublic($starif_materiel["prix_public"]);
                  $materiel->setReferenceFabricant($starif_materiel["reference_fabricant"]);
                  $materiel->setCommentaire($starif_materiel["commentaire"]);
                  $type = $this->typeRepository->findOneBy(["starif_id" => $starif_materiel["type_id"]]);
                  if ($type instanceof Type) {
                    $materiel->setType($type);
                  }
                  $this->entityManager->persist($materiel);
                }
              }
            }
          }
          $this->entityManager->flush();
          break;
        default :

          break;
      }
    }
}
