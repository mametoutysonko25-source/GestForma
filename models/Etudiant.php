<?php
class Etudiant {
    private $conn;
    private $table = "etudiant";

    public $idUtilisateur;
    public $matricule;
    public $nom;
    public $prenom;
    public $email;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getEtudiantById($idUtilisateur) {
        $query = "SELECT e.idUtilisateur, e.matricule, u.nom, u.prenom, u.email 
                  FROM " . $this->table . " e 
                  INNER JOIN utilisateur u ON e.idUtilisateur = u.idUtilisateur 
                  WHERE e.idUtilisateur = :idUtilisateur";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idUtilisateur", $idUtilisateur);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>