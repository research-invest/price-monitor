docker-compose up -d --force-recreate --no-deps --build monitor-adminer
docker-compose up -d --force-recreate --no-deps --build monitor-php

docker exec -it monitor-php bash

php artisan migrate
php artisan storage:link