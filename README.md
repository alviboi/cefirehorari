# Introducció

Es tracta d'una aplicació per permetre el fitxatge d'assessors del CEFIRE, per  poder fer-lo funcionar, es recomana instal·lar-lo en un servidor independent. Totes les proves s'han fet en una Ubuntu 20.04. És possible que hi haja coses que no funcionen, poseu-se en contacte amb mi per corregir-les. L'aplicació està sofrint modificacions de manera continua.

# Clonar el projecte

Per a clonar el projecte podem utilitzar el següent comandament:

```
git clone --recurse-submodules https://github.com/alviboi/projecte_ceedcv
```

> Has de tenir instal·lat els **paquets docker docker-compose**:


# Instal·lació de l'aplicació

Una vegada descarregat, hem executar el següent comandament que trobarem dins de la carpeta arrel:

```
./start.sh
```
El comandament tardarà uns minuts en fer totes les operacions.

Per a poder entrar en el workspace del docker, pots utilitzar el següent comandament:


    sudo docker-compose exec --user=laradock workspace bash


L'usuari per a poder entrat es admin@admin, i la contrasenya admin. Es recomana llegir l'ajuda de l'aplicació per a veure com funciona

# Dispositiu de fixatges

Podeu consultar el [següent projecte](https://github.com/alviboi/arduino_lector_rfid) per veure un prototip del dispositiu de fitxatge que es podria utilitzar.

# Opcions

Podeu habilitar ssl en nginx, entrant a l'arxiu ./laradock/nginx/sites/default.conf i descomentant el següent paràmetres:

```
# For https
    listen 443 ssl default_server;
    listen [::]:443 ssl default_server ipv6only=on;
    ssl_certificate /etc/nginx/ssl/default.crt;
    ssl_certificate_key /etc/nginx/ssl/default.key;
```

Abans d'aplicar el script de start.sh, i si aneu a utilitzar la importació de LDAP cal canviar l'arxiu ./laradock/php-fpm/Dockerfile el paràmetre INSTALL_LDAP a true

```
    ARG INSTALL_LDAP=true
```

A l'arxiu ./laradock/workspace/Dockerfile canviar el següent:

```
    ARG INSTALL_LDAP=false

    RUN if [ ${INSTALL_LDAP} = true ]; then \

        apt-get install -y libldap2-dev && \
        apt-get install -y php${LARADOCK_PHP_VERSION}-ldap \
    ;fi
```
per

```
    ARG INSTALL_LDAP=true

    RUN if [ ${INSTALL_LDAP} = true ]; then \
        apt-get update && \
        apt-get install -y libldap2-dev && \
        apt-get install -y php${LARADOCK_PHP_VERSION}-ldap \
    ;fi
```

Dins de l'arxiu .env que trobareu dins de la carpeta cefire (una vegada has executat start.sh), has de configurar els següents paràmetres si vols fer ús de websockets i el correu has d'omplir les claus:

```
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=eu
BROADCAST_DRIVER=pusher

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"
```

Dins de l'arxiu .env de la carpeta laradock cal configurar els següents paràmetres per a la teua base de dades:

```
WORKSPACE_INSTALL_LDAP=true


MYSQL_VERSION=latest
MYSQL_DATABASE=
MYSQL_USER=
MYSQL_PASSWORD=
MYSQL_PORT=3306
MYSQL_ROOT_PASSWORD=

```

# PHP Worker

Has d'afegir l'arxiu cefire.conf dins de la carperta ./laradock/php-worker/supervisord.d/

```
[program:cefire]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/artisan queue:work --sleep=3 --tries=3 --daemon
autostart=true
autorestart=true
user=root
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/storage/logs/supervisordcefire.log
```