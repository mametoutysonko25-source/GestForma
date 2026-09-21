<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Paiement.php';

class PaiementController {
    private $db;
    private $paiementModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->paiementModel = new Paiement($this->db);
    }

    public function enregistrerPaiement($montant, $modePaiement, $reference, $idInscription) {
        return $this->paiementModel->enregistrerPaiement($montant, $modePaiement, $reference, $idInscription);
    }

    public function getInscriptionsValidees() {
        return $this->paiementModel->getInscriptionsValidees();
    }

    public function getPaiementsByInscription($idInscription) {
        return $this->paiementModel->getPaiementsByInscription($idInscription);
    }

    public function getTotalPaye($idInscription) {
        return $this->paiementModel->getTotalPaye($idInscription);
    }

    public function getAllPaiements() {
        return $this->paiementModel->getAllPaiements();
    }

    public function getPaiement($idPaiement) {
        return $this->paiementModel->getById($idPaiement);
    }

    public function modifierPaiement($idPaiement, $montant, $modePaiement, $reference) {
        return $this->paiementModel->modifierPaiement($idPaiement, $montant, $modePaiement, $reference);
    }

    public function supprimerPaiement($idPaiement) {
        return $this->paiementModel->supprimerPaiement($idPaiement);
    }
}
?>