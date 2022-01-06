init: up
up:
	docker-compose up -d
recreate-tg:
	docker-compose up -d --force-recreate --no-deps --build tg-service
goapi:
	docker-compose exec api-php bash
gotgs:
	docker-compose exec tg-service sh