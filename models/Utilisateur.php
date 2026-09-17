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

    /**
     * Crée la ligne commune UTILISATEUR et retourne son idUtilisateur.
     * $statutInitial vaut 'actif' pour un étudiant (auto-inscription
     * immédiatement utilisable), ou 'en_attente' pour un responsable
     * pédagogique / comptable dont le compte doit être validé.
     */
    private static function createUtilisateur(string $nom, string $prenom, string $email, string $telephone, string $nomUtilisateur, string $motDePasse, string $statutInitial = 'actif'): int
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
            'statut'         => $statutInitial,
        ]);
        return (int) $pdo->lastInsertId();
    }

    /**
     * Génère un matricule du type PREFIXE-ANNEE-001 en se basant sur le
     * nombre de lignes déjà présentes dans la table du rôle concerné.
     */
    private static function genererMatricule(string $table, string $prefixe): string
    {
        $pdo = getPDO();
        $annee = date('Y');
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM {$table}");
        $rang = ((int) $stmt->fetch()['total']) + 1;
        return sprintf('%s-%s-%03d', $prefixe, $annee, $rang);
    }

    /**
     * Vrai si l'e-mail ou le nom d'utilisateur est déjà pris.
     */
    public static function emailOuNomUtilisateurExiste(string $email, string $nomUtilisateur): bool
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT 1 FROM UTILISATEUR WHERE email = :email OR nomUtilisateur = :nomUtilisateur LIMIT 1');
        $stmt->execute(['email' => $email, 'nomUtilisateur' => $nomUtilisateur]);
        return (bool) $stmt->fetch();
    }

    /**
     * Inscription d'un étudiant.
     */
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
            $idUtilisateur = self::createUtilisateur($nom, $prenom, $email, $telephone, $nomUtilisateur, $motDePasse, 'actif');
            $matricule = self::genererMatricule('ETUDIANT', 'ETU');

            $stmt = $pdo->prepare(
                'INSERT INTO ETUDIANT (idUtilisateur, matricule, dateNaissance, lieuNaissance, sexe, dateInscription, statutParcours)
                 VALUES (:id, :matricule, :dateNaissance, :lieuNaissance, :sexe, NOW(), :statutParcours)'
            );
            $stmt->execute([
                'id'             => $idUtilisateur,
                'matricule'      => $matricule,
                'dateNaissance'  => $dateNaissance,
                'lieuNaissance'  => $lieuNaissance,
                'sexe'           => $sexe,
                'statutParcours' => 'dossier_incomplet',
            ]);

            $pdo->commit();
            return $idUtilisateur;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Inscription publique d'un responsable pédagogique (en attente de validation).
     */
    public static function createResponsable(
        string $nom,
        string $prenom,
        string $email,
        string $telephone,
        string $nomUtilisateur,
        string $motDePasse,
        string $specialite
    ): int {
        $pdo = getPDO();
        $pdo->beginTransaction();
        try {
            $idUtilisateur = self::createUtilisateur($nom, $prenom, $email, $telephone, $nomUtilisateur, $motDePasse, 'en_attente');
            $matricule = self::genererMatricule('RESPONSABLE_PEDAGOGIQUE', 'RP');

            $stmt = $pdo->prepare(
                'INSERT INTO RESPONSABLE_PEDAGOGIQUE (idUtilisateur, matricule, specialite, datePriseFonction)
                 VALUES (:id, :matricule, :specialite, NULL)'
            );
            $stmt->execute([
                'id'         => $idUtilisateur,
                'matricule'  => $matricule,
                'specialite' => $specialite,
            ]);

            $pdo->commit();
            return $idUtilisateur;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Inscription publique d'un comptable (en attente de validation).
     */
    public static function createComptable(
        string $nom,
        string $prenom,
        string $email,
        string $telephone,
        string $nomUtilisateur,
        string $motDePasse
    ): int {
        $pdo = getPDO();
        $pdo->beginTransaction();
        try {
            $idUtilisateur = self::createUtilisateur($nom, $prenom, $email, $telephone, $nomUtilisateur, $motDePasse, 'en_attente');

            $stmt = $pdo->prepare('INSERT INTO COMPTABLE (idUtilisateur) VALUES (:id)');
            $stmt->execute(['id' => $idUtilisateur]);

            $pdo->commit();
            return $idUtilisateur;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
