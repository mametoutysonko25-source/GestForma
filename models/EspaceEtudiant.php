<?php

class EspaceEtudiant
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    private function inscriptionValidee(int $idEtudiant): ?int
    {
        $stmt = $this->conn->prepare(
            'SELECT i.idInscription
             FROM inscription i
             INNER JOIN dossier_etudiant d ON d.idDossier = i.idDossier
             WHERE d.idEtudiant = :idEtudiant AND i.statut = "VALIDEE"
             ORDER BY i.idInscription DESC LIMIT 1'
        );
        $stmt->execute([':idEtudiant' => $idEtudiant]);
        $id = $stmt->fetchColumn();
        return $id === false ? null : (int) $id;
    }

    private function moduleIds(int $idEtudiant): array
    {
        $stmt = $this->conn->prepare(
            'SELECT m.idModule
             FROM module m
             INNER JOIN semestre s ON s.idSemestre = m.idSemestre
             INNER JOIN niveau n ON n.idNiveau = s.idNiveau
             INNER JOIN inscription i ON i.idNiveau = n.idNiveau
             INNER JOIN dossier_etudiant d ON d.idDossier = i.idDossier
             WHERE d.idEtudiant = :idEtudiant AND i.statut = "VALIDEE"'
        );
        $stmt->execute([':idEtudiant' => $idEtudiant]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public function getDossier(int $idEtudiant): ?array
    {
        $stmt = $this->conn->prepare(
            'SELECT e.*, u.nom, u.prenom, u.email, d.idDossier, d.anneeScolaire,
                    d.statut AS statutDossier, i.idInscription, i.statut AS statutInscription,
                    i.dateInscription, n.libelle AS niveau
             FROM etudiant e
             INNER JOIN utilisateur u ON u.idUtilisateur = e.idUtilisateur
             LEFT JOIN dossier_etudiant d ON d.idEtudiant = e.idUtilisateur
             LEFT JOIN inscription i ON i.idDossier = d.idDossier
             LEFT JOIN niveau n ON n.idNiveau = i.idNiveau
             WHERE e.idUtilisateur = :idEtudiant
             ORDER BY d.idDossier DESC, i.idInscription DESC LIMIT 1'
        );
        $stmt->execute([':idEtudiant' => $idEtudiant]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getNiveaux(): array
    {
        return $this->conn->query('SELECT idNiveau, libelle FROM niveau ORDER BY ordre')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPlanning(int $idEtudiant): array
    {
        $stmt = $this->conn->prepare(
            'SELECT se.dateSeance, se.heureDebut, se.heureFin, se.statut,
                    m.libelle AS module, u.nom, u.prenom
             FROM seance se
             INNER JOIN module m ON m.idModule = se.idModule
             INNER JOIN formateur f ON f.idUtilisateur = se.idFormateur
             INNER JOIN utilisateur u ON u.idUtilisateur = f.idUtilisateur
             WHERE se.idModule IN (' . $this->modulePlaceholder($idEtudiant) . ')
             ORDER BY se.dateSeance, se.heureDebut'
        );
        $stmt->execute($this->moduleParams($idEtudiant));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getModules(int $idEtudiant): array
    {
        $ids = $this->moduleIds($idEtudiant);
        if (!$ids) return [];
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->conn->prepare(
            'SELECT m.*, s.libelle AS semestre FROM module m
             INNER JOIN semestre s ON s.idSemestre = m.idSemestre
             WHERE m.idModule IN (' . $marks . ') ORDER BY s.ordre, m.libelle'
        );
        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSupports(int $idEtudiant): array
    {
        $ids = $this->moduleIds($idEtudiant);
        if (!$ids) return [];
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->conn->prepare(
            'SELECT sp.*, m.libelle AS module FROM support sp
             INNER JOIN module m ON m.idModule = sp.idModule
             WHERE sp.idModule IN (' . $marks . ') ORDER BY sp.dateAjout DESC'
        );
        $stmt->execute($ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEvaluations(int $idEtudiant): array
    {
        $ids = $this->moduleIds($idEtudiant);
        if (!$ids) return [];
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->conn->prepare(
            'SELECT ev.*, m.libelle AS module, dp.idDepot, dp.fichier, dp.dateDepot
             FROM evaluation ev INNER JOIN module m ON m.idModule = ev.idModule
             LEFT JOIN depot dp ON dp.idEvaluation = ev.idEvaluation AND dp.idEtudiant = ?
             WHERE ev.idModule IN (' . $marks . ') ORDER BY ev.dateLimite'
        );
        $stmt->execute(array_merge([$idEtudiant], $ids));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResultats(int $idEtudiant): array
    {
        $stmt = $this->conn->prepare(
            'SELECT r.*, ev.titre, ev.dateLimite, m.libelle AS module
             FROM resultat r INNER JOIN evaluation ev ON ev.idEvaluation = r.idEvaluation
             INNER JOIN module m ON m.idModule = ev.idModule
             WHERE r.idEtudiant = :idEtudiant ORDER BY r.datePublication DESC'
        );
        $stmt->execute([':idEtudiant' => $idEtudiant]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFinance(int $idEtudiant): array
    {
        $stmt = $this->conn->prepare(
            'SELECT i.idInscription, i.statut, i.dateInscription, n.libelle AS niveau,
                    COALESCE(SUM(p.montant), 0) AS totalPaye,
                    COUNT(p.idPaiement) AS nombrePaiements
             FROM inscription i INNER JOIN dossier_etudiant d ON d.idDossier = i.idDossier
             INNER JOIN niveau n ON n.idNiveau = i.idNiveau
             LEFT JOIN paiement p ON p.idInscription = i.idInscription
             WHERE d.idEtudiant = :idEtudiant
             GROUP BY i.idInscription, i.statut, i.dateInscription, n.libelle
             ORDER BY i.idInscription DESC'
        );
        $stmt->execute([':idEtudiant' => $idEtudiant]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deposerTravail(int $idEvaluation, int $idEtudiant, string $fichier): bool
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO depot (idEvaluation, idEtudiant, fichier, dateDepot)
             VALUES (:idEvaluation, :idEtudiant, :fichier, NOW())
             ON DUPLICATE KEY UPDATE fichier = VALUES(fichier), dateDepot = NOW()'
        );
        return $stmt->execute(compact('idEvaluation', 'idEtudiant', 'fichier'));
    }

    private function modulePlaceholder(int $idEtudiant): string
    {
        $count = count($this->moduleIds($idEtudiant));
        return $count ? implode(',', array_fill(0, $count, '?')) : 'NULL';
    }

    private function moduleParams(int $idEtudiant): array
    {
        return $this->moduleIds($idEtudiant);
    }
}