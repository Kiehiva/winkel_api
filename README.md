# Winkel - Projet de fin d'année

## Serveur
### Technologies
    - Symfony
    - API Platform

### Instructions
Cloner le repo et installer les dépendances avec 
```bash
    composer install
```
Lancer le serveur:
```bash
    php -S localhost:8000 -t public
```
*OU*
```bash
    symfony serve
```
**L'application sera dispobible à l'addresse: "http://localhost:8000"**

Pour les personnes utilisant *Docker*, lancer le service "mysql":
```bash
    docker-compose up -d
```
La base de donnée se configurera seule.

# winkel_api
