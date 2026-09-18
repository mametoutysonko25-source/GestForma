<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/../models/Utilisateur.php';

class AuthController extends BaseController
{
    public function login(): void
    {
        $email      = trim($this->post('email', ''));
        $motDePasse = $this->post('password', '');
        $role       = $this->post('role', '');

        $rolesValides = array_keys(User::ROLE_TABLES);
        if ($email === '' || $motDePasse === '' || !in_array($role, $rolesValides, true)) {
                $this->redirect('/views/auth/login.php?erreur=champs_invalides');
        }

        try {
            $user = User::findForLogin($email, $role);
        } catch (PDOException $exception) {
            $erreur = (int) $exception->getCode() === 2002
                ? 'serveur_bdd_arrete'
                : 'connexion_indisponible';
            $this->redirect('/views/auth/login.php?erreur=' . $erreur);
        }

        if (!$user || !User::verifyPassword($motDePasse, $user['motDePasseHash'])) {
                $this->redirect('/views/auth/login.php?erreur=identifiants_incorrects');
        }

        if (($user['statutCompte'] ?? null) === 'en_attente') {
            $this->redirect('/views/auth/login.php?erreur=compte_en_attente');
        }

        if (!User::estActif($user)) {
                $this->redirect('/views/auth/login.php?erreur=compte_desactive');
        }

        // Connexion réussie.
        regenerateSession();
        try {
            User::updateLastLogin((int) $user['idUtilisateur']);
        } catch (PDOException $exception) {
            $erreur = (int) $exception->getCode() === 2002
                ? 'serveur_bdd_arrete'
                : 'connexion_indisponible';
            $this->redirect('/views/auth/login.php?erreur=' . $erreur);
        }

        $_SESSION['user'] = [
            'id'     => $user['idUtilisateur'],
            'nom'    => trim($user['prenom'] . ' ' . $user['nom']), // nom d'affichage (navbar, sidebar)
            'prenom' => $user['prenom'],
            'nomFamille' => $user['nom'],
            'email'  => $user['email'],
            'role'   => $role,
        ];
        $_SESSION['nom_utilisateur'] = $_SESSION['user']['nom'];
        $_SESSION['role_utilisateur'] = $role;

        $this->redirect(DASHBOARD_PAR_ROLE[$role] ?? '/index.php');
    }

    public function register(): void
    {
        $role           = $this->post('role', '');
        $nom            = trim($this->post('nom', ''));
        $prenom         = trim($this->post('prenom', ''));
        $email          = trim($this->post('email', ''));
        $telephone      = trim($this->post('telephone', ''));
        $nomUtilisateur = trim($this->post('nomUtilisateur', ''));
        $motDePasse     = $this->post('password', '');
        $motDePasse2    = $this->post('password2', '');

        $rolesInscriptibles = ['etudiant', 'responsable', 'comptable'];
        if (!in_array($role, $rolesInscriptibles, true)) {
            $this->redirect('/views/auth/register.php?erreur=role_invalide');
        }

        if ($nom === '' || $prenom === '' || $email === '' || $nomUtilisateur === '' || $motDePasse === '') {
            $this->redirect("/views/auth/register.php?erreur=champs_invalides&role={$role}");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect("/views/auth/register.php?erreur=email_invalide&role={$role}");
        }

        if ($motDePasse !== $motDePasse2) {
            $this->redirect("/views/auth/register.php?erreur=mots_de_passe_differents&role={$role}");
        }

        if (User::emailOuNomUtilisateurExiste($email, $nomUtilisateur)) {
            $this->redirect("/views/auth/register.php?erreur=deja_utilise&role={$role}");
        }

        try {
            if ($role === 'etudiant') {
                $dateNaissance = $this->post('dateNaissance', '');
                $lieuNaissance = trim($this->post('lieuNaissance', ''));
                $sexe          = $this->post('sexe', '');

                if ($dateNaissance === '' || $lieuNaissance === '' || $sexe === '') {
                    $this->redirect("/views/auth/register.php?erreur=champs_invalides&role={$role}");
                }

                User::createEtudiant($nom, $prenom, $email, $telephone, $nomUtilisateur, $motDePasse, $dateNaissance, $lieuNaissance, $sexe);
                $this->redirect('/views/auth/login.php?inscription=etudiant_ok');
            }

            if ($role === 'responsable') {
                $specialite = trim($this->post('specialite', ''));
                if ($specialite === '') {
                    $this->redirect("/views/auth/register.php?erreur=champs_invalides&role={$role}");
                }

                User::createResponsable($nom, $prenom, $email, $telephone, $nomUtilisateur, $motDePasse, $specialite);
                $this->redirect('/views/auth/login.php?inscription=en_attente');
            }

            if ($role === 'comptable') {
                User::createComptable($nom, $prenom, $email, $telephone, $nomUtilisateur, $motDePasse);
                $this->redirect('/views/auth/login.php?inscription=en_attente');
            }
        } catch (Exception $e) {
            $this->redirect("/views/auth/register.php?erreur=technique&role={$role}");
        }
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path']);
        }
        session_destroy();
        $this->redirect('/views/auth/login.php');
    }
}


$controller = new AuthController();

Router::dispatch([
    'login'    => [$controller, 'login'],
    'register' => [$controller, 'register'],
    'logout'   => [$controller, 'logout'],
]);
