# Winkel - Projet de fin d'année

## Serveur
### Technologies
    - Symfony
    - API Platform

### Instructions
Cloner le repo et installer les dépendances 
```bash
    composer install
```
*La suite des instructions se feront avec l'idée que vous avez la CLI de Symfony et Docker installé*

Lancer le service mysql
```bash
    docker-compose up -d
```
Le service se lancera en arrière plan et la base de donnée se configurera automatiquement.

Créer la base de donnée
```bash
    symfony console doctrine:migrations:migrate
```

Lancer le serveur
```bash
    symfony serve -d
```

### Arrêt 
Stopper le serveur Symfony
```bash
    symfony server:stop
```

Stopper le container Docker
```bash
    docker-compose down
```
*Powered by moi*