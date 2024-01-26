<?php

namespace App\Entity;

use App\Repository\MaterielRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterielRepository::class)]
class Materiel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_court = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $marque = null;

    #[ORM\Column(nullable: true)]
    private ?float $prix_public = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reference_fabricant = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'materiels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $type = null;

    #[ORM\Column]
    private ?int $starif_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNomCourt(): ?string
    {
        return $this->nom_court;
    }

    public function setNomCourt(?string $nom_court): static
    {
        $this->nom_court = $nom_court;

        return $this;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(?string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getPrixPublic(): ?float
    {
        return $this->prix_public;
    }

    public function setPrixPublic(?float $prix_public): static
    {
        $this->prix_public = $prix_public;

        return $this;
    }

    public function getReferenceFabricant(): ?string
    {
        return $this->reference_fabricant;
    }

    public function setReferenceFabricant(?string $reference_fabricant): static
    {
        $this->reference_fabricant = $reference_fabricant;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStarifId(): ?int
    {
        return $this->starif_id;
    }

    public function setStarifId(int $starif_id): static
    {
        $this->starif_id = $starif_id;

        return $this;
    }

    // Convert the object to an array
    public function to_array(): Array 
    {
      $type = $this->getType();
      $metier = $this->getType()->getMetier();

      $materiel = [];
      $materiel["id"] = $this->getStarifId();
      $materiel["nom_court"] = $this->getNomCourt();
      $materiel["nom"] = $this->getNom();
      $materiel["marque"] = $this->getMarque();
      $materiel["prix_public"] = $this->getPrixPublic();
      $materiel["reference_fabricant"] = $this->getReferenceFabricant();
      $materiel["commentaire"] = $this->getCommentaire();
      $materiel["type"] = [
        "id" => $type->getStarifId(),
        "famille" => $type->getFamille(),
        "nom" => $type->getNom(),
        "metier" => [
          "id" => $metier->getStarifId(),
          'nom' => $metier->getNom()
        ],
      ];

      return $materiel;
    }
}
