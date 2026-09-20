<?php
class Paiement {
    private $conn;
    private $table = "paiement";

    public $idPaiement;
    public $montant;
    public $datePaiement;
    public $modePaiement;
    public $reference;
    public $idInscription;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function enregistrerPaiement($montant, $modePaiement, $reference, $idInscription) {
        $query = "INSERT INTO " . $this->table . " (montant, datePaiement, modePaiement, referencePaiement, idInscription)
              VALUES (:montant, CURDATE(), :modePaiement, :reference, :idInscription)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":montant", $montant);
        $stmt->bindParam(":modePaiement", $modePaiement);
        $stmt->bindParam(":reference", $reference);
        $stmt->bindParam(":idInscription", $idInscription);
        return $stmt->execute();
    }

    public function getPaiementsByInscription($idInscription) {
        $query = "SELECT * FROM " . $this->table . " WHERE idInscription = :idInscription ORDER BY datePaiement DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idInscription", $idInscription);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaiementById($idPaiement) {
        $query = "SELECT * FROM " . $this->table . " WHERE idPaiement = :idPaiement";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':idPaiement' => (int) $idPaiement]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function modifierPaiement($idPaiement, $montant, $modePaiement, $reference) {
        $query = "UPDATE " . $this->table . "
                  SET montant = :montant, modePaiement = :modePaiement, referencePaiement = :reference
                  WHERE idPaiement = :idPaiement";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':montant' => $montant,
            ':modePaiement' => $modePaiement,
            ':reference' => $reference !== '' ? $reference : null,
            ':idPaiement' => (int) $idPaiement,
        ]);
    }

    public function supprimerPaiement($idPaiement) {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE idPaiement = :idPaiement");
        return $stmt->execute([':idPaiement' => (int) $idPaiement]);
    }

    public function getTotalPaye($idInscription) {
        $query = "SELECT COALESCE(SUM(montant), 0) AS total FROM " . $this->table . " WHERE idInscription = :idInscription";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idInscription", $idInscription);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getAllPaiements() {
        $query = "SELECT p.*, i.idInscription, i.statut AS statut_inscription,
                         d.anneeScolaire, e.matricule, u.nom, u.prenom, u.email,
                         n.libelle AS niveau_libelle
                  FROM " . $this->table . " p
                  INNER JOIN inscription i ON p.idInscription = i.idInscription
                  INNER JOIN dossier_etudiant d ON i.idDossier = d.idDossier
                  INNER JOIN etudiant e ON d.idEtudiant = e.idUtilisateur
                  INNER JOIN utilisateur u ON e.idUtilisateur = u.idUtilisateur
                  INNER JOIN niveau n ON i.idNiveau = n.idNiveau
                  ORDER BY p.datePaiement DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInscriptionsValidees() {
        $query = "SELECT i.idInscription, i.statut, i.dateInscription,
                         d.anneeScolaire, d.idEtudiant,
                         e.matricule, u.nom, u.prenom, u.email,
                         n.libelle AS niveau_libelle,
                         COALESCE((SELECT SUM(montant) FROM PAIEMENT WHERE idInscription = i.idInscription), 0) AS total_paye
                  FROM inscription i
                  INNER JOIN dossier_etudiant d ON i.idDossier = d.idDossier
                  INNER JOIN etudiant e ON d.idEtudiant = e.idUtilisateur
                  INNER JOIN utilisateur u ON e.idUtilisateur = u.idUtilisateur
                  INNER JOIN niveau n ON i.idNiveau = n.idNiveau
                  WHERE i.statut = 'VALIDEE'
                  ORDER BY i.dateInscription DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>