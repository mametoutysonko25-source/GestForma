<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/../models/User.php';

class ResponsableController extends BaseController
{
    public function comptesEnAttente(): void
    {
        $this->requireRole(['responsable']);
        header('Location: ' . BASE_URL . 'views/responsable/comptes-en-attente.php');
        exit;
    }

    public function valider(): void
    {
        $this->requireRole(['responsable']);
        $id = (int) $this->post('idUtilisateur', 0);
        if ($id > 0) {
            User::validerCompte($id);
        }
        $this->redirect('/views/responsable/comptes-en-attente.php?statut=valide');
    }

    public function modifier(): void
    {
        $this->requireRole(['responsable']);
        $id         = (int) $this->post('idUtilisateur', 0);
        $matricule  = trim($this->post('matricule', ''));
        $specialite = trim($this->post('specialite', ''));

        if ($id > 0 && $matricule !== '') {
            User::modifierResponsableEnAttente($id, $matricule, $specialite);
        }
        $this->redirect('/views/responsable/comptes-en-attente.php?statut=modifie');
    }

    public function refuser(): void
    {
        $this->requireRole(['responsable']);
        $id = (int) $this->post('idUtilisateur', 0);
        if ($id > 0) {
            User::refuserCompte($id);
        }
        $this->redirect('/views/responsable/comptes-en-attente.php?statut=refuse');
    }
}

$controller = new ResponsableController();

Router::dispatch([
    'comptesEnAttente' => [$controller, 'comptesEnAttente'],
    'valider'          => [$controller, 'valider'],
    'modifier'         => [$controller, 'modifier'],
    'refuser'          => [$controller, 'refuser'],
]);
