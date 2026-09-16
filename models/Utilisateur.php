<?php

require_once __DIR__ . '/../config/database.php';

class User
{
    
    public const ROLE_TABLES = [
        'etudiant'       => 'ETUDIANT',
        'formateur'      => 'FORMATEUR',
        'responsable'    => 'RESPONSABLE_PEDAGOGIQUE',
        'comptable'      => 'COMPTABLE',
        'directeur'      => 'DIRECTEUR',
        'administrateur' => 'ADMINISTRATEUR',
    ];

    
    public static function findForLogin(string $email, string $role): ?array
    {
        if (!isset(self::ROLE_TABLES[$role])) {
            return null;
        }
        $table = self::ROLE_TABLES[$role];

        $pdo = getPDO();
     
        $sql = "SELECT u.*, r.*
                FROM UTILISATEUR u
                INNER JOIN {$table} r ON r.idUtilisateur = u.idUtilisateur
                WHERE u.email = :email
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    
    public static function verifyPassword(string $motDePasseClair, string $hash): bool
    {
        return password_verify($motDePasseClair, $hash);
    }

    public static function estActif(array $user): bool
    {
        return ($user['statutCompte'] ?? null) === 'actif';
    }

    public static function updateLastLogin(int $idUtilisateur): void
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('UPDATE UTILISATEUR SET derniereConnexion = NOW() WHERE idUtilisateur = :id');
        $stmt->execute(['id' => $idUtilisateur]);
    }

   
    private static function createUtilisateur(string $nom, string $prenom, string $email, string $telephone, string $nomUtilisateur, string $motDePasse): int
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            'INSERT INTO UTILISATEUR (nom, prenom, email, telephone, nomUtilisateur, motDePasseHash, statutCompte, dateCreation)
             VALUES (:nom, :prenom, :email, :telephone, :nomUtilisateur, :hash, :statut, NOW())'
        );
        $stmt->execute([
            'nom'            => $nom,
            'prenom'         => $prenom,
            'email'          => $email,
            'telephone'      => $telephone,
            'nomUtilisateur' => $nomUtilisateur,
            'hash'           => password_hash($motDePasse, PASSWORD_DEFAULT),
            'statut'         => 'actif',
        ]);
        return (int) $pdo->lastInsertId();
    }

   
    public static function createEtudiant(
        string $nom,
        string $prenom,
        string $email,
        string $telephone,
        string $nomUtilisateur,
        string $motDePasse,
        string $dateNaissance,
        string $lieuNaissance,
        string $sexe
    ): int {
        $pdo = getPDO();
        $pdo->beginTransaction();
        try {
            $idUtilisateur = self::createUtilisateur($nom, $prenom, $email, $telephone, $nomUtilisateur, $motDePasse);

            $stmt = $pdo->prepare(
                'INSERT INTO ETUDIANT (idUtilisateur, dateNaissance, lieuNaissance, sexe, dateInscription, statutParcours)
                 VALUES (:id, :dateNaissance, :lieuNaissance, :sexe, NOW(), :statutParcours)'
            );
            $stmt->execute([
                'id'            => $idUtilisateur,
                'dateNaissance' => $dateNaissance,
                'lieuNaissance' => $lieuNaissance,
                'sexe'          => $sexe,
                'statutParcours'=> 'dossier_incomplet',
            ]);

            $pdo->commit();
            return $idUtilisateur;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
