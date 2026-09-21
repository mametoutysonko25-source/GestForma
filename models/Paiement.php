<?php
class Paiement {
    private $conn;
    private $table = "PAIEMENT";

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
        $query = "INSERT INTO " . $this->table . " (montant, modePaiement, referencePaiement, idInscription) 
                  VALUES (:montant, :modePaiement, :referencePaiement, :idInscription)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":montant", $montant);
        $stmt->bindParam(":modePaiement", $modePaiement);
        $stmt->bindParam(":referencePaiement", $reference);
        $stmt->bindParam(":idInscription", $idInscription);
        return $stmt->execute();
    }

    public function getPaiementsByInscription($idInscription) {
        $query = "SELECT p.*, p.referencePaiement AS reference FROM " . $this->table . " p WHERE idInscription = :idInscription ORDER BY datePaiement DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idInscription", $idInscription);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                        p.referencePaiement AS reference, n.libelle AS niveau_libelle
                  FROM " . $this->table . " p
                  INNER JOIN INSCRIPTION i ON p.idInscription = i.idInscription
                  INNER JOIN DOSSIER_ETUDIANT d ON i.idDossier = d.idDossier
                  INNER JOIN ETUDIANT e ON d.idEtudiant = e.idUtilisateur
                  INNER JOIN UTILISATEUR u ON e.idUtilisateur = u.idUtilisateur
                  INNER JOIN NIVEAU n ON i.idNiveau = n.idNiveau
                  ORDER BY p.datePaiement DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($idPaiement) {
        $query = "SELECT p.*, p.referencePaiement AS reference, i.idInscription, d.anneeScolaire,
                 e.matricule, u.nom, u.prenom, n.libelle AS niveau_libelle
                  FROM " . $this->table . " p
                  INNER JOIN INSCRIPTION i ON p.idInscription = i.idInscription
                  INNER JOIN DOSSIER_ETUDIANT d ON i.idDossier = d.idDossier
                  INNER JOIN ETUDIANT e ON d.idEtudiant = e.idUtilisateur
                  INNER JOIN UTILISATEUR u ON e.idUtilisateur = u.idUtilisateur
                  INNER JOIN NIVEAU n ON i.idNiveau = n.idNiveau
                  WHERE p.idPaiement = :idPaiement";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['idPaiement' => $idPaiement]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function modifierPaiement($idPaiement, $montant, $modePaiement, $reference) {
        $query = "UPDATE " . $this->table . "
                  SET montant = :montant, modePaiement = :modePaiement, referencePaiement = :reference
                  WHERE idPaiement = :idPaiement";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'idPaiement' => $idPaiement,
            'montant' => $montant,
            'modePaiement' => $modePaiement,
            'reference' => $reference ?: null,
        ]);
    }

    public function supprimerPaiement($idPaiement) {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE idPaiement = :idPaiement");
        return $stmt->execute(['idPaiement' => $idPaiement]);
    }

    public function getInscriptionsValidees() {
        $query = "SELECT i.idInscription, i.statut, i.dateInscription,
                         d.anneeScolaire, d.idEtudiant,
                         e.matricule, u.nom, u.prenom, u.email,
                         n.libelle AS niveau_libelle,
                         COALESCE((SELECT SUM(montant) FROM PAIEMENT WHERE idInscription = i.idInscription), 0) AS total_paye
                  FROM INSCRIPTION i
                  INNER JOIN DOSSIER_ETUDIANT d ON i.idDossier = d.idDossier
                  INNER JOIN ETUDIANT e ON d.idEtudiant = e.idUtilisateur
                  INNER JOIN UTILISATEUR u ON e.idUtilisateur = u.idUtilisateur
                  INNER JOIN NIVEAU n ON i.idNiveau = n.idNiveau
                  WHERE i.statut = 'VALIDEE'
                  ORDER BY i.dateInscription DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>