# Projet S4

## Comment participer au projet ?

1. Git clone

        $ git clone https://iut-info.univ-reims.fr/gitlab/verd0012/e-commerce.git

2. Démarrer le serveur

        $ composer start

3. Créer la BD
    - Dupliquer le fichier .env et le renommer en .env.local
    - Modifier la ligne commençant par DATABASE_URL
    - Créer la BD avec la commande
        
            $ bin/console doctrine:schema:create
    
    - Générer les données avec la commande

            $ bin/console doctrine:fixtures:load


## Le projet

### Les criteres de tri

Les articles peuvent être triés a l'aide de plusieurs filtres :

        http://127.0.0.1:8000/article?libelle=jupe&section=homme&critere_tri=prix_u&tri_ordre=DESC&taille=L&type_article=jupe&categorie=vetement&prix_entre=20_30&description=pull