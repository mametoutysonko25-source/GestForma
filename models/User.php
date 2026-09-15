<?php


require_once __DIR__ . '/../config/database.php';

class User
{
    /**
     * Association clé de rôle (utilisée dans l'application et les URLs)
     * → nom de la table correspondante en base.
     */
    public const ROLE_TABLES = [
        'etudiant'       => 'ETUDIANT',
        'formateur'      => 'FORMATEUR',
        'responsable'    => 'RESPONSABLE_PEDAGOGIQUE',
        'comptable'      => 'COMPTABLE',
        'directeur'      => 'DIRECTEUR',
        'administrateur' => 'ADMINISTRATEUR',
    ];

    /**
     * Recherche un utilisateur actif par e-mail, pour un rôle donné,
     * en joignant UTILISATEUR à sa table-rôle. Retourne un tableau
     * associatif fusionnant les deux tables, ou null si non trouvé.
     */
    public static function findForLogin(string $email, string $role): ?array
    {
        if (!isset(self::ROLE_TABLES[$role])) {
            return null;
        }
        $table = self::ROLE_TABLES[$role];

        $pdo = getPDO();
        // Nom de table validé via la liste blanche ROLE_TABLES ci-dessus :
        // pas d'injection possible même si on l'interpole directement.
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

    /**
     * Vérifie le mot de passe en clair contre le hash stocké
     * (colonne motDePasseHash).
     */
    public static function verifyPassword(string $motDePasseClair, string $hash): bool
    {
        return password_verify($motDePasseClair, $hash);
    }

    /**
     * vrai si le compte est actif (statutCompte = 'actif').
     * Centralise la valeur attendue plutôt que de la répéter partout.
     */
    public static function estActif(array $user): bool
    {
        return ($user['statutCompte'] ?? null) === 'actif';
    }

    /**
     * Met à jour la date de dernière connexion, appelée juste après un
     * login réussi.
     */
    public static function updateLastLogin(int $idUtilisateur): void
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare('UPDATE UTILISATEUR SET derniereConnexion = NOW() WHERE idUtilisateur = :id');
        $stmt->execute(['id' => $idUtilisateur]);
    }

    /**
     * Crée la ligne commune UTILISATEUR et retourne son idUtilisateur.
     * Utilisé par createEtudiant() et, plus tard, par les écrans
     * d'administration créant des comptes formateur/comptable/etc.
     */
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

    /**
     * Inscription d'un étudiant (seul rôle en auto-inscription — voir
     * section 2.6 : "L'Étudiant peut : créer son compte").
     * Les autres rôles sont créés par l'Administrateur ou le
     * Responsable pédagogique, pas via un formulaire public.
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
