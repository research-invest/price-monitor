init: up composer_install migrate seed
up:
	docker-compose up -d
recreate-tg:
	docker-compose up -d --force-recreate --no-deps --build tg-service
composer_install:
	docker-compose run --rm api-php composer install
migrate:
	docker-compose run --rm api-php php artisan migrate --force
seed:
	docker-compose run --rm api-php php artisan db:seed
goapi:
	docker-compose exec api-php bash
get-prices:
	docker-compose exec api-php php artisan get-prices:run
gotgs:
	docker-compose exec tg-service sh