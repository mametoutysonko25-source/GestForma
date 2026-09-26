<?php
class DossierEtudiant {
    private $conn;
    private $table = "DOSSIER_ETUDIANT";

    public $idDossier;
    public $anneeScolaire;
    public $statut;
    public $idEtudiant;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function creerDossier($idEtudiant, $anneeScolaire) {
        $query = "INSERT INTO " . $this->table . " (anneeScolaire, statut, idEtudiant) 
                  VALUES (:anneeScolaire, :statut, :idEtudiant)";
        $stmt = $this->conn->prepare($query);
        $statut = "ACTIF";
        $stmt->bindParam(":anneeScolaire", $anneeScolaire);
        $stmt->bindParam(":statut", $statut);
        $stmt->bindParam(":idEtudiant", $idEtudiant);
        return $stmt->execute();
    }

    public function getDossierByEtudiant($idEtudiant) {
        $query = "SELECT * FROM " . $this->table . " WHERE idEtudiant = :idEtudiant ORDER BY idDossier DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idEtudiant", $idEtudiant);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDossierByEtudiantEtAnnee($idEtudiant, $anneeScolaire) {
        $query = "SELECT * FROM " . $this->table . " WHERE idEtudiant = :idEtudiant AND anneeScolaire = :anneeScolaire ORDER BY idDossier DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idEtudiant", $idEtudiant);
        $stmt->bindParam(":anneeScolaire", $anneeScolaire);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getIdDossier() {
        return $this->conn->lastInsertId();
    }
}
?>