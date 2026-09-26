<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/helpers.php';
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
                $this->redirect('views/auth/login.php?erreur=champs_invalides');
        }

        try {
            $user = User::findForLogin($email, $role);
        } catch (PDOException $exception) {
            $erreur = (int) $exception->getCode() === 2002
                ? 'serveur_bdd_arrete'
                : 'connexion_indisponible';
            $this->redirect('views/auth/login.php?erreur=' . $erreur);
        }

        if (!$user || !User::verifyPassword($motDePasse, $user['motDePasseHash'])) {
                $this->redirect('views/auth/login.php?erreur=identifiants_incorrects');
        }

        if (!User::estActif($user)) {
                $this->redirect('views/auth/login.php?erreur=compte_desactive');
        }

        // Connexion réussie.
        regenerateSession();
        try {
            User::updateLastLogin((int) $user['idUtilisateur']);
            journaliserAction('Connexion', 'Connexion réussie le ' . date('d/m/Y à H:i:s'), (int) $user['idUtilisateur']);
        } catch (PDOException $exception) {
            $erreur = (int) $exception->getCode() === 2002
                ? 'serveur_bdd_arrete'
                : 'connexion_indisponible';
            $this->redirect('views/auth/login.php?erreur=' . $erreur);
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

        $this->redirect(DASHBOARD_PAR_ROLE[$role] ?? 'index.php');
    }

    public function logout(): void
    {
        if (isset($_SESSION['user']['id'])) {
            journaliserAction('Déconnexion', 'Déconnexion demandée le ' . date('d/m/Y à H:i:s'), (int) $_SESSION['user']['id']);
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path']);
        }
        session_destroy();
        $this->redirect('views/auth/login.php');
    }
}


$controller = new AuthController();

Router::dispatch([
    'login'  => [$controller, 'login'],
    'logout' => [$controller, 'logout'],
]);
