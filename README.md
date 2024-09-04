<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

Projet pédagogique Simplon 2024

Gestion de transactions

Dase de données :

    - 1 table User
    - 1 table Transaction
    En relation 1 to many

Un contrôlleur User permettant:

    - De s'enregistrer
    - De se connecter
    - De consulter les utilisateurs (si connecté)
    - De modifier ses informations (si connecté)
    - De supprimer son compte

Un contrôlleur Transaction permettant:

    - Créer une transaction
    - Modifier une transaction
    - Supprimer une transaction
    - Voir ses transaction

Un utilisateur ne peux consulter et intéragir uniquement avec ses transactions.
Le fait d'être Admin ne donne aucun accès sur les transactions des utilisateurs.
Le fait d'être admin permet de consulter tous les utilisateurs, de les modifier (pour les rendre Admin par exemple) ou de les supprimer (non respect des conditions d'utlisation ou compte inactif depuis trop longtemps)
