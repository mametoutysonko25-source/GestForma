# Projet GestForm - Groupe 3

## Description du Projet

GestForm est une application web PHP de gestion de formation développée dans le cadre des travaux universitaires. Elle implémente un système modulaire d'interfaces communes (Header, Navbar, Sidebar, Footer) avec une gestion dynamique des rôles utilisateurs.

## Structure et Arborescence du Projet

- **`index.php`** : Page d'accueil principale et point d'entrée de l'application.
- **`includes/`** : Contient les composants PHP modulaires partagés :
  - `header.php` : En-tête de page.
  - `navbar.php` : Barre de navigation supérieure.
  - `sidebar.php` : Menu latéral dynamique.
  - `footer.php` : Pied de page.
- **`assets/`** : Ressources graphiques et scripts du projet :
  - **`css/`** : Feuilles de style individuelles (`footer.css`, `header.css`, `navbar.css`, `sidebar.css`, `style.css`).
  - **`js/`** : Scripts JavaScript (`header.js`, `navbar.js`, `sidebar.js`).

## Installation locale

1. Créez une base MySQL/MariaDB nommée `gestform`.
2. Importez `databases/gestform.sql` dans cette base.
3. Vérifiez les paramètres de connexion dans `config/database.php`.
4. Lancez l'application avec `php -S localhost:8000` depuis la racine du projet.

### Comptes de démonstration

Après l'import de `databases/gestform.sql`, les comptes suivants sont disponibles :

| E-mail | Mot de passe | Rôle à sélectionner |
| --- | --- | --- |
| admin@centre-formation.sn | Admin@2025 | Administrateur |
| directeur@centre-formation.sn | Directeur@2025 | Directeur |
| pedagogie@centre-formation.sn | Pedagogie@2025 | Responsable |
| formateur@centre-formation.sn | Formateur@2025 | Formateur |
| comptable@centre-formation.sn | Comptable@2025 | Comptable |
| etudiant@centre-formation.sn | Etudiant@2025 | Étudiant |
