# Winkel - Projet de fin d'année

## Serveur
### Pre-requis
    - Symfony CLI
    - Docker-Compose
    - PHP8

### Technologies
    - Symfony
    - API Platform

### Instructions
Cloner le repo et installer les dépendances 
```bash
    composer install
```
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

L'API sera dispo à l'addresse:
```bash
    http://localhost:8000/api
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