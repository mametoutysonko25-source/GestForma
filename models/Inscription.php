<?php
class Inscription {
    private $conn;
    private $table = "INSCRIPTION";

    public $idInscription;
    public $dateInscription;
    public $statut;
    public $idDossier;
    public $idNiveau;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function demanderInscription($idDossier, $idNiveau) {
        $query = "INSERT INTO " . $this->table . " (dateInscription, statut, idDossier, idNiveau) 
                  VALUES (CURDATE(), :statut, :idDossier, :idNiveau)";
        $stmt = $this->conn->prepare($query);
        $statut = "EN_ATTENTE";
        $stmt->bindParam(":statut", $statut);
        $stmt->bindParam(":idDossier", $idDossier);
        $stmt->bindParam(":idNiveau", $idNiveau);
        return $stmt->execute();
    }

    public function getInscriptionsEnAttente() {
        $query = "SELECT i.idInscription, i.dateInscription, i.statut, i.idNiveau,
                         d.idDossier, d.anneeScolaire, d.idEtudiant,
                         e.matricule, u.nom, u.prenom, u.email,
                         n.libelle AS niveau_libelle
                  FROM " . $this->table . " i
                  INNER JOIN DOSSIER_ETUDIANT d ON i.idDossier = d.idDossier
                  INNER JOIN ETUDIANT e ON d.idEtudiant = e.idUtilisateur
                  INNER JOIN UTILISATEUR u ON e.idUtilisateur = u.idUtilisateur
                  INNER JOIN NIVEAU n ON i.idNiveau = n.idNiveau
                  WHERE i.statut = 'EN_ATTENTE'
                  ORDER BY i.dateInscription DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function validerInscription($idInscription) {
        $query = "UPDATE " . $this->table . " SET statut = 'VALIDEE' WHERE idInscription = :idInscription";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idInscription", $idInscription);
        return $stmt->execute();
    }

    public function refuserInscription($idInscription) {
        $query = "UPDATE " . $this->table . " SET statut = 'REFUSEE' WHERE idInscription = :idInscription";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idInscription", $idInscription);
        return $stmt->execute();
    }

    public function getInscriptionByDossier($idDossier) {
        $query = "SELECT * FROM " . $this->table . " WHERE idDossier = :idDossier ORDER BY idInscription DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idDossier", $idDossier);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getInscriptionById($idInscription) {
        $query = "SELECT i.*, n.libelle AS niveau_libelle, d.anneeScolaire,
                         e.matricule, u.nom, u.prenom, u.email
                  FROM " . $this->table . " i
                  INNER JOIN DOSSIER_ETUDIANT d ON i.idDossier = d.idDossier
                  INNER JOIN ETUDIANT e ON d.idEtudiant = e.idUtilisateur
                  INNER JOIN UTILISATEUR u ON e.idUtilisateur = u.idUtilisateur
                  INNER JOIN NIVEAU n ON i.idNiveau = n.idNiveau
                  WHERE i.idInscription = :idInscription";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idInscription", $idInscription);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getIdInscription() {
        return $this->conn->lastInsertId();
    }
}
?>