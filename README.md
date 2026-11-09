# Demo Application: PHP + Docker + HashiCorp Vault + MySQL + phpMyAdmin

Ce projet est une démonstration d'une application PHP s'interfaçant avec un serveur **HashiCorp Vault** pour récupérer de manière sécurisée les identifiants de connexion à une base de données **MySQL**, le tout orchestré par **Docker Compose**.

---

## 🛠️ Architecture

* **Web (`demo_web`)** : Serveur Apache avec PHP 8.2 (extensions `pdo_mysql` et `curl`).
* **Vault (`demo_vault`)** : Serveur de gestion de secrets HashiCorp Vault (Mode Dev).
* **Database (`demo_db`)** : Base de données MySQL 8.0.
* **phpMyAdmin (`demo_phpmyadmin`)** : Interface web de gestion de la base de données (accessible sur le port 8081).

---

## 📋 Prérequis

* **Docker** et **Docker Compose** installés sur votre machine.
* **Git** et un client **SSH** configuré.

---

## 🚀 Guide de démarrage (How-To)

### 1. Cloner le projet

```bash
git clone git@github.com:votre-utilisateur/votre-projet.git
cd votre-projet
```

### 2. Configurer les variables d'environnement

Copiez le fichier d'exemple .env.example vers .env :

```Bash
cp .env.example .env
```

(Optionnel) Vous pouvez modifier les mots de passe et le token Vault dans le fichier .env généré.

### 3. Démarrer les conteneurs Docker

Lancez les services en arrière-plan :
```Bash

docker compose up -d --build

```
    Note : Grâce aux healthchecks, l'application PHP contiendra son démarrage jusqu'à ce que MySQL et Vault soient complètement opérationnels.

### 4. Injecter les identifiants dans Vault

Avant que l'application PHP ne puisse se connecter à la base de données, vous devez enregistrer les identifiants dans Vault.

Exécutez la commande suivante dans votre terminal :

```Bash

docker compose exec -e VAULT_ADDR="[http://127.0.0.1:8200](http://127.0.0.1:8200)" vault vault kv put secret/db \
    host="db" \
    dbname="app_db" \
    username="app_user" \
    password="app_password"

```

(Si vous avez modifié les identifiants dans votre fichier .env, veillez à aligner les valeurs ci-dessus).
### 5. Accéder aux services

* **Application PHP** : [http://localhost:8080](http://localhost:8080)
  * L'application interroge Vault via son API REST, récupère les identifiants `db`, `username` et `password`, puis s'y connecte via PDO.
* **phpMyAdmin** : [http://localhost:8081](http://localhost:8081)
  * **Serveur** : `db`
  * **Utilisateur** : `root` (ou `app_user`)
  * **Mot de passe** : défini dans `.env`
* **Interface Vault UI** : [http://localhost:8200](http://localhost:8200)
  * **Token** : `myroottoken` (ou la valeur spécifiée dans `.env`)

## 🛠️ Commandes utiles

Vérifier l'état des conteneurs :
```Bash

docker compose ps
```
Consulter les logs de l'application web :

```Bash

docker compose logs -f web
```
Accéder au shell du conteneur Vault :
```Bash

docker compose exec vault sh
```
Arrêter et supprimer les conteneurs :
```Bash
docker compose down
```

---