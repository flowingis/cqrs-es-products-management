# Cqrs Event sourcing workshop


Requisiti
=

Se vuoi utilizzare vagrant:
- Vagrant (> 1.8.x) con plugin HostManager (vagrant plugin install vagrant-hostmanager)
- Virtualbox (>= 5.1.28)

Se vuoi utilizzare Docker:
- Docker

Configurazione del progetto con Vagrant
=======================================

Scaricare la box al seguente indirizzo: https://drive.google.com/file/d/1w_5_3YSJ8h3rlFGwxSkP9KOBPj1mazkk/view?usp=sharing 
e salvarla nel PATH/ROOT/DEL/PROGETTO

```
cd PATH/ROOT/DEL/PROGETTO
vagrant box add new-cqrs-es-ws new-cqrs-es-ws.box
vagrant box list -> dovrebbe mostrare la box appena aggiunta

vagrant up --provider=virtualbox
vagrant ssh
cd /var/www
```

Configurazione del progetto con Docker
======================================
 
```
cd PATH/ROOT/DEL/PROGETTO
docker-compose up -d
```

Per lanciare i test con Vagrant
===============================

```
cd PATH/ROOT/DEL/PROGETTO
vagrant ssh
cd /var/www
./idephix.phar buildVagrant
```

Per lanciare i test con Docker
===============================

```
cd PATH/ROOT/DEL/PROGETTO
docker-compose exec php ./idephix.phar test
```

Per lanciare la build con Docker
================================

```
cd PATH/ROOT/DEL/PROGETTO
docker-compose exec php ./idephix.phar test
```

Utilizzare le api dell'applicazione con Vagrant
==============================================

Dalla macchina locale

```
curl -X POST http://api.cqrsws.lo/app_dev.php/products
curl -X PUT http://api.cqrsws.lo/app_dev.php/products/{uuid prodotto}
curl -X GET http://api.cqrsws.lo/app_dev.php/products/{uuid prodotto}
```

Tools con Vagrant
================

- Adminer: http://db.cqrsws.lo/
    - server: localhost
    - user: ideato
    - password: ideato


Utilizzare le api dell'applicazione con Docker
==============================================

Dalla macchina locale

```
curl -X POST http://localhost:8080/app_dev.php/products
curl -X PUT http://localhost:8080/app_dev.php/products/{uuid prodotto}
curl -X GET http://localhost:8080/app_dev.php/products/{uuid prodotto}
```

Tools con Docker
================

- Adminer: http://localhost:8081/
    - server: mysql
    - user: ideato
    - password: ideato
