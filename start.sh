 #!/bin/bash
mv env-laradock ./laradock/.env
mv env-cefire ./cefire/.env
cd ./laradock

 sudo systemctl start docker
#

 sudo docker-compose up -d nginx mysql phpmyadmin php-worker
 sudo docker-compose exec workspace bash /var/www/instalar.sh
