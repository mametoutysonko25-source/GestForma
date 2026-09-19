<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/DossierEtudiant.php';
require_once __DIR__ . '/../models/Inscription.php';

class InscriptionController {
    private $db;
    private $dossierModel;
    private $inscriptionModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->dossierModel = new DossierEtudiant($this->db);
        $this->inscriptionModel = new Inscription($this->db);
    }

    public function demanderInscription($idEtudiant, $idNiveau, $anneeScolaire) {
        // Vérifier si un dossier existe déjà
        $dossier = $this->dossierModel->getDossierByEtudiant($idEtudiant);
        
        if (!$dossier) {
            // Créer un nouveau dossier
            $this->dossierModel->creerDossier($idEtudiant, $anneeScolaire);
            $idDossier = $this->dossierModel->getIdDossier();
        } else {
            $idDossier = $dossier['idDossier'];
        }

        // Créer la demande d'inscription
        return $this->inscriptionModel->demanderInscription($idDossier, $idNiveau);
    }

    public function getInscriptionsEnAttente() {
        return $this->inscriptionModel->getInscriptionsEnAttente();
    }

    public function validerInscription($idInscription) {
        return $this->inscriptionModel->validerInscription($idInscription);
    }

    public function refuserInscription($idInscription) {
        return $this->inscriptionModel->refuserInscription($idInscription);
    }

    public function getEtatInscription($idEtudiant) {
        $dossier = $this->dossierModel->getDossierByEtudiant($idEtudiant);
        if (!$dossier) {
            return null;
        }
        return $this->inscriptionModel->getInscriptionByDossier($dossier['idDossier']);
    }
}
?>