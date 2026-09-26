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
    private $referenceColumn;

    public function __construct($db) {
        $this->conn = $db;
		$this->referenceColumn = null;
		foreach ($this->conn->query('SHOW COLUMNS FROM PAIEMENT')->fetchAll(PDO::FETCH_ASSOC) as $column) {
			if (in_array(strtolower($column['Field']), ['reference', 'referencepaiement'], true)) {
				$this->referenceColumn = $column['Field'];
				break;
			}
		}
    }

    public function enregistrerPaiement($montant, $modePaiement, $reference, $idInscription) {
        $colonnes = ['montant', 'modePaiement', 'datePaiement', 'idInscription'];
        $valeurs = [':montant', ':modePaiement', 'CURRENT_TIMESTAMP', ':idInscription'];
        if ($this->referenceColumn !== null) {
            $colonnes[] = '`' . $this->referenceColumn . '`';
            $valeurs[] = ':reference';
        }
        $query = 'INSERT INTO ' . $this->table . ' (' . implode(', ', $colonnes) . ') VALUES (' . implode(', ', $valeurs) . ')';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":montant", $montant);
        $stmt->bindParam(":modePaiement", $modePaiement);
        $stmt->bindParam(":idInscription", $idInscription);
        if ($this->referenceColumn !== null) {
            $reference = trim((string) $reference);
            $reference = $reference !== '' ? $reference : null;
            $stmt->bindParam(":reference", $reference);
        }
        return $stmt->execute();
    }

    private function referenceSelect(string $alias = ''): string {
        $prefix = $alias !== '' ? $alias . '.' : '';
        return $this->referenceColumn === null
            ? 'NULL AS reference'
            : $prefix . '`' . $this->referenceColumn . '` AS reference';
    }

    public function getPaiementsByInscription($idInscription) {
        $query = "SELECT p.*, " . $this->referenceSelect('p') . " FROM " . $this->table . " p WHERE p.idInscription = :idInscription ORDER BY p.datePaiement DESC";
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
        $query = "SELECT p.*, " . $this->referenceSelect('p') . ", i.idInscription, i.statut AS statut_inscription,
                         d.anneeScolaire, e.matricule, u.nom, u.prenom, u.email,
                         n.libelle AS niveau_libelle
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