<?php

class Remuneration
{
    public function __construct(private PDO $conn) {}

    public function all(): array
    {
        return $this->conn->query(
            'SELECT r.*, f.matricule, u.nom, u.prenom FROM remuneration_formateur r
             INNER JOIN formateur f ON f.idUtilisateur = r.idFormateur
             INNER JOIN utilisateur u ON u.idUtilisateur = f.idUtilisateur
             ORDER BY r.mois DESC, u.nom, u.prenom'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function formateurs(): array
    {
        return $this->conn->query(
            'SELECT f.idUtilisateur, f.matricule, u.nom, u.prenom
             FROM formateur f INNER JOIN utilisateur u ON u.idUtilisateur = f.idUtilisateur
             ORDER BY u.nom, u.prenom'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->conn->prepare('SELECT * FROM remuneration_formateur WHERE idRemuneration = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function save(array $data): bool
    {
        $sql = empty($data['idRemuneration'])
            ? 'INSERT INTO remuneration_formateur (idFormateur, mois, heuresValidees, montantDu, montantPaye, statut) VALUES (?, ?, ?, ?, ?, ?)'
            : 'UPDATE remuneration_formateur SET idFormateur = ?, mois = ?, heuresValidees = ?, montantDu = ?, montantPaye = ?, statut = ? WHERE idRemuneration = ?';
        $values = [(int) $data['idFormateur'], $data['mois'], $data['heuresValidees'], $data['montantDu'], $data['montantPaye'], $data['statut']];
        if (!empty($data['idRemuneration'])) $values[] = (int) $data['idRemuneration'];
        return $this->conn->prepare($sql)->execute($values);
    }

    public function delete(int $id): bool
    {
        return $this->conn->prepare('DELETE FROM remuneration_formateur WHERE idRemuneration = ?')->execute([$id]);
    }
}