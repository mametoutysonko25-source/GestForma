<?php

require_once __DIR__ . '/../config/database.php';

class Directeur
{
    public static function statistiques(): array
    {
        $requete = database()->query(
            "SELECT
                (SELECT COUNT(*) FROM etudiant) AS etudiants,
                (SELECT COUNT(*) FROM formation) AS formations,
                (SELECT COUNT(*) FROM formateur) AS formateurs,
                (SELECT COUNT(*) FROM responsable_pedagogique) AS responsables,
                (SELECT COUNT(*) FROM comptable) AS comptables,
                (SELECT COUNT(*) FROM administrateur) AS administrateurs,
                (SELECT COUNT(*) FROM directeur) AS directeurs,
                (SELECT COALESCE(SUM(montant), 0) FROM paiement
                    WHERE MONTH(datePaiement) = MONTH(CURRENT_DATE())
                    AND YEAR(datePaiement) = YEAR(CURRENT_DATE())) AS recettes_mois,
                (SELECT COUNT(*) FROM resultat WHERE note IS NOT NULL) AS resultats,
                (SELECT COUNT(*) FROM resultat WHERE note >= 10) AS reussites,
                (SELECT COUNT(*) FROM presence) AS presences,
                (SELECT COUNT(*) FROM presence
                    WHERE UPPER(statutPresence) IN ('PRESENT', 'PRESENTE')) AS presents"
        );

        $statistiques = $requete->fetch() ?: [];
        $statistiques['personnel'] = array_sum([
            (int) ($statistiques['formateurs'] ?? 0),
            (int) ($statistiques['responsables'] ?? 0),
            (int) ($statistiques['comptables'] ?? 0),
            (int) ($statistiques['administrateurs'] ?? 0),
            (int) ($statistiques['directeurs'] ?? 0),
        ]);
        $statistiques['taux_reussite'] = self::pourcentage($statistiques['reussites'] ?? 0, $statistiques['resultats'] ?? 0);
        $statistiques['taux_presence'] = self::pourcentage($statistiques['presents'] ?? 0, $statistiques['presences'] ?? 0);

        return $statistiques;
    }

    public static function personnel(): array
    {
        $requete = database()->query(
            "SELECT u.prenom, u.nom, u.email, u.telephone, u.statutCompte, 'Directeur' AS role
             FROM utilisateur u INNER JOIN directeur p ON p.idUtilisateur = u.idUtilisateur
             UNION ALL
             SELECT u.prenom, u.nom, u.email, u.telephone, u.statutCompte, 'Responsable pédagogique'
             FROM utilisateur u INNER JOIN responsable_pedagogique p ON p.idUtilisateur = u.idUtilisateur
             UNION ALL
             SELECT u.prenom, u.nom, u.email, u.telephone, u.statutCompte, 'Formateur'
             FROM utilisateur u INNER JOIN formateur p ON p.idUtilisateur = u.idUtilisateur
             UNION ALL
             SELECT u.prenom, u.nom, u.email, u.telephone, u.statutCompte, 'Comptable'
             FROM utilisateur u INNER JOIN comptable p ON p.idUtilisateur = u.idUtilisateur
             UNION ALL
             SELECT u.prenom, u.nom, u.email, u.telephone, u.statutCompte, 'Administrateur'
             FROM utilisateur u INNER JOIN administrateur p ON p.idUtilisateur = u.idUtilisateur
             ORDER BY nom, prenom"
        );

        return $requete->fetchAll();
    }

    private static function pourcentage($numerateur, $denominateur): int
    {
        return (int) $denominateur > 0 ? (int) round(((int) $numerateur / (int) $denominateur) * 100) : 0;
    }
}