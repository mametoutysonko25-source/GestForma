<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/EspaceEtudiant.php';

class EspaceEtudiantController
{
    private EspaceEtudiant $model;

    public function __construct()
    {
        $this->model = new EspaceEtudiant(database());
    }

    public function dossier(int $id): ?array { return $this->model->getDossier($id); }
    public function niveaux(): array { return $this->model->getNiveaux(); }
    public function planning(int $id): array { return $this->model->getPlanning($id); }
    public function modules(int $id): array { return $this->model->getModules($id); }
    public function supports(int $id): array { return $this->model->getSupports($id); }
    public function evaluations(int $id): array { return $this->model->getEvaluations($id); }
    public function resultats(int $id): array { return $this->model->getResultats($id); }
    public function finance(int $id): array { return $this->model->getFinance($id); }
    public function deposer(int $evaluation, int $etudiant, string $fichier): bool
    {
        return $this->model->deposerTravail($evaluation, $etudiant, $fichier);
    }
}