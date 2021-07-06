# Projet Symfony BDD no Sql

Projet crée par Samy Letombe, Margaux Lemoine, Lina Chakhchoukh et Philippe Fidalgo.

# Tutoriel de instalation

Voici comment lancer le projet!!

## Setup Symfony

Pour pour voir lancer ce projet il vous faudra installer composer + php + symfony.

### Symfony + Composer

Le projet est installé avec la version 5.0.11 de Symfony, il vous faudra si possible installer cette version, voici la doc:  

https://symfony.com/doc/5.0/setup.html

La doc precise également comment installer composer !

**Attention !** Cette version n'est plus maintenue pour symfony alors dites nous si vous rencontrez des soucis d'installation.

Pour voir quelle version vous avez, lancer la commande :

>php bin/console about

Attention il faudra utiliser la version 7.4 de PHP!!

### Php
On utilise du php 7.4 donc il faudra installer cette version pour ce projet!

Pour vérifier votre version vous avez lancer la commande:
> php -v 

Pour voir quel version de php vous avez dans votre machine lancer:
> symfony local:php:list

Si vous n'avez pas installer **php version 7.4** alors, telechargez cette version.

Pour que le projet utilise la version php 7.2, créez un fichier : 

*.php-version*

Avec à l'intérieur :
>7.4

## Setup BDD

On utilise ici 3 bases de données : une sql et deux No sql.

### SQL
Il faudra lancer une bbd mysql, l'url de connexion est dans le fichier:

*.env* sur la variable DATABASE_URL

Pour plus d'infos, nous vous invitons à regarder la doc symfony:

https://symfony.com/doc/current/doctrine.html

### Elasticsearch
Il faudra lancer une bbd elasticsearch, l'url de connexion est dans le fichier:

*.env* sur la variable ELASTICSEARCH_URL


On utilise ici la version 6.8 de elastic search, car la dernière version n'est pas compatible avec le bundle:

https://github.com/FriendsOfSymfony/FOSElasticaBundle/blob/master/doc/usage.md


### Redis
L'url de redis est dans le fichier:

*.env*

Avec la variable REDIS_URL


Pour pouvoir utliser redis sur votre php 7.4, aller sur son répertoir d'installation et lancez la commande :
> redis install

(exemple sur mac)
> /usr/local/opt/php\@7.4/bin/pecl install redis

Si vous ne l'intallez pas vous ne pourrez pas vous connectez à redis et tester toute les fonctionalitées

## Lancer le projet

### Composer install
Avant tout lancer toutes vos bbd:
- mySql
- Redis
- Elasticsearch

Pour lancer le projet, nous avons mis à votre disposition quelques fichiers pour pouvoir effectuer des test.

Tout d'abord lancer la commande :

>composer install 

Cela va telecharger toutes vos dépandances dans un dossier vendor.

Puis verifier que ce fichier existe : 

*config/packages/fos_elastica.yaml*

Si non, créez le et remplissez le avec les données de ce fichier:

*projectFiles/error_elasticsearch.yaml*

Si le fichier existe, verifier que le contenu est similaire à celui de *error_elasticsearch.yaml*

### Remplir votre base mySql

Créer votre BD sur symfony:
>  php bin/console doctrine:database:create

Après cela,  mettre à jour vos entités
>  php bin/console doctrine:schema:update --force

Pour avoir des utilisateurs en base de données (vous pourrez toujours en créer plus):
> php bin/console d:f:l

Attention! Pour ce test il faut que le Revendeur 1 et le Revendeur 2 aient un id de 1 et 2!!! 

Sinon vous aurez des erreurs lors de l'import des données mySql.
(Éditer les valeurs si nécessaire)

Aller sur *projectFiles/sql_dump.sql* et copier coller le sql pour remplir la liste de produits.

### Remplir votre Elasticsearch

Pour remplir votre elasticsearch lancer la commande:
> php bin/console fos:elastica:populate

Elle va indexer nos produits dans le site !

Il n'y a pas d'update pour les données elasticsearch, alors si il y a un changement dans la base de données Sql il faudras relancer la commande pour reindexer les produits.

### Lancer le projet

Lancer la commande :
> symfony server:start

Votre projet sera ouvert sur :

**http://127.0.0.1:8000/connexion**

Les comptes par defaut:

Email: user1@user1.fr
Password: 12345678

Email: king@king.fr
Password: 12345678

Email: pick@me.fr
Password: 12345678


### Contact

N'hesitez pas à nous contacter sur Slack ou autre si vous rencontrez des soucis lors de l'installation :) .


