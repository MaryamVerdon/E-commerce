# Projet S4

## Comment participer au projet ?

1. Git clone

        $ git clone https://iut-info.univ-reims.fr/gitlab/verd0012/e-commerce.git

2. Démarrer le serveur

        $ composer start

3. Installer les dépendances si besoin

        $ composer install

4. Créer la BD
    - Dupliquer le fichier .env et le renommer en .env.local
    - Modifier la ligne commençant par DATABASE_URL
    - Créer la BD avec la commande
        
            $ bin/console doctrine:schema:create
    
    - Générer les données avec la commande

            $ bin/console doctrine:fixtures:load

    - Creer la table session

            CREATE TABLE `sessions` (
            `sess_id` VARCHAR(128) NOT NULL PRIMARY KEY,
            `sess_data` BLOB NOT NULL,
            `sess_time` INTEGER UNSIGNED NOT NULL,
            `sess_lifetime` INTEGER UNSIGNED NOT NULL
            ) COLLATE utf8mb4_bin, ENGINE = InnoDB;

## Le projet

### L'authentification

La connection se fait par le biais de l'email du client.

Le compte admin a pour adresse mail "admin@gmail.com", pour les autres vous devez vous réferencer à votre bd.

Le mot de passe par default pour tout les comptes est "azerty"

L'accès au compte du client connecté se fait par le biais de la route :

        http://127.0.0.1:8000/compte

Ses commandes sont accessible depuis :

        http://127.0.0.1:8000/compte/commandes

L'administration est accessible depuis :

        http://127.0.0.1:8000/admin

### Les criteres de tri

Les articles peuvent être triés a l'aide de plusieurs filtres :

        http://127.0.0.1:8000/article?libelle=jupe&section=homme&critere_tri=prix_u&tri_ordre=DESC&taille=L&type_article=jupe&categorie=vetement&prix_entre=20_30&description=pull

### Pagination

La liste des articles est paginé.

Elle a comme valeur par default la page 1 et 20 articles par page.

La page et le nombre d'articles par page peuvent etre passé en parametre

        http://127.0.0.1:8000/article?page=1&nb_max_par_page=30

Les parametres actuels sont retournes au template en string depuis l'attribut "pagination.params" il sufit d'y donc d'y ajouter le parametre de la page demmandé.

La numero de la page et de la derniere page sont accessible depuis les attributs "pagination.page" et "pagination.nbPages".

### Le panier

Le panier est accessible depuis :

        http://127.0.0.1:8000/panier

Le panier est stocké dans les données de session.

Il est accessible partout depuis le servise App\Service\Panier\PanierService

Il est possible d'ajouter/supprimer un article :

        http://127.0.0.1:8000/panier/add/{id}?taille={idTaille}

        http://127.0.0.1:8000/panier/add/{id}?taille={idTaille}&quantite={quantite}

        http://127.0.0.1:8000/panier/remove/{id}?taille={idTaille}

### Idées


### A faire

[Voir les Issues](https://iut-info.univ-reims.fr/gitlab/verd0012/e-commerce/issues)
