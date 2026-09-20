<?php
class DossierEtudiant {
    private $conn;
    private $table = "dossier_etudiant";

    public $idDossier;
    public $anneeScolaire;
    public $statut;
    public $idEtudiant;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function creerDossier($idEtudiant, $anneeScolaire) {
        $query = "INSERT INTO " . $this->table . " (anneeScolaire, statut, dateCreation, idEtudiant)
              VALUES (:anneeScolaire, :statut, CURDATE(), :idEtudiant)";
        $stmt = $this->conn->prepare($query);
        $statut = "ACTIF";
        $stmt->bindParam(":anneeScolaire", $anneeScolaire);
        $stmt->bindParam(":statut", $statut);
        $stmt->bindParam(":idEtudiant", $idEtudiant);
        return $stmt->execute();
    }

    public function getDossierByEtudiant($idEtudiant, $anneeScolaire = null) {
        $query = "SELECT * FROM " . $this->table . " WHERE idEtudiant = :idEtudiant";
        if ($anneeScolaire !== null) {
            $query .= " AND anneeScolaire = :anneeScolaire";
        }
        $query .= " ORDER BY idDossier DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":idEtudiant", (int) $idEtudiant, PDO::PARAM_INT);
        if ($anneeScolaire !== null) {
            $stmt->bindValue(":anneeScolaire", $anneeScolaire);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getIdDossier() {
        return $this->conn->lastInsertId();
    }
}
?>