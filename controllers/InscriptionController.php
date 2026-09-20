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
        $anneeScolaire = trim((string) $anneeScolaire);
        if (!preg_match('/^\d{4}-\d{4}$/', $anneeScolaire) || (int) $idNiveau <= 0 || (int) $idEtudiant <= 0) {
            return false;
        }

        $this->db->beginTransaction();
        try {
            $dossier = $this->dossierModel->getDossierByEtudiant($idEtudiant, $anneeScolaire);
            if (!$dossier) {
                if (!$this->dossierModel->creerDossier($idEtudiant, $anneeScolaire)) {
                    throw new RuntimeException('Création du dossier impossible.');
                }
                $idDossier = $this->dossierModel->getIdDossier();
            } else {
                $idDossier = $dossier['idDossier'];
            }

            if ($this->inscriptionModel->getInscriptionByDossier($idDossier)) {
                $this->db->rollBack();
                return false;
            }
            if (!$this->inscriptionModel->demanderInscription($idDossier, $idNiveau)) {
                throw new RuntimeException('Création de l’inscription impossible.');
            }
            $this->db->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
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