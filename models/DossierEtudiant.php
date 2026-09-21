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
        $query = "INSERT INTO " . $this->table . " (anneeScolaire, statut, dateCreation, idEtudiant) 
              VALUES (:anneeScolaire, :statut, CURRENT_DATE, :idEtudiant)";
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

    public function getIdDossier() {
        return $this->conn->lastInsertId();
    }

    public function modifierDossier($idDossier, $anneeScolaire, $statut) {
        $stmt = $this->conn->prepare(
            "UPDATE " . $this->table . " SET anneeScolaire = :anneeScolaire, statut = :statut WHERE idDossier = :idDossier"
        );
        return $stmt->execute([
            'idDossier' => $idDossier,
            'anneeScolaire' => $anneeScolaire,
            'statut' => $statut,
        ]);
    }

    public function supprimerDossier($idDossier) {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE idDossier = :idDossier");
        return $stmt->execute(['idDossier' => $idDossier]);
    }
}
?>