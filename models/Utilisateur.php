<?php

require_once __DIR__ . '/../config/database.php';

class User
{
	public const ROLE_TABLES = [
		'etudiant' => 'etudiant',
		'formateur' => 'formateur',
		'responsable' => 'responsable_pedagogique',
		'comptable' => 'comptable',
		'administrateur' => 'administrateur',
		'directeur' => 'directeur',
	];

	public static function findForLogin(string $email, string $role): ?array
	{
		if (!isset(self::ROLE_TABLES[$role])) {
			return null;
		}

		$roleTable = self::ROLE_TABLES[$role];
		$sql = "SELECT u.idUtilisateur, u.nom, u.prenom, u.email,
					   u.motDePasseHash, u.statutCompte
				FROM utilisateur u
				INNER JOIN {$roleTable} r ON r.idUtilisateur = u.idUtilisateur
				WHERE LOWER(u.email) = LOWER(:email)
				LIMIT 1";
		$statement = database()->prepare($sql);
		$statement->execute(['email' => $email]);
		$user = $statement->fetch();

		return $user ?: null;
	}

	public static function verifyPassword(string $password, string $hash): bool
	{
		return password_verify($password, $hash);
	}

	public static function estActif(array $user): bool
	{
		return strtoupper((string) ($user['statutCompte'] ?? '')) === 'ACTIF';
	}

	public static function updateLastLogin(int $id): void
	{
		$statement = database()->prepare(
			'UPDATE utilisateur SET derniereConnexion = CURRENT_TIMESTAMP WHERE idUtilisateur = :id'
		);
		$statement->execute(['id' => $id]);
	}
}
