init: up monitor-init
up:
	docker-compose up -d
monitor-init:
	docker-compose run --rm monitor-php composer install
	migrate
	docker-compose run --rm monitor-php php artisan db:seed
migrate:
	docker-compose run --rm monitor-php php artisan migrate --force
get-channel-info:
	docker-compose exec monitor-php php artisan get-channel-info:run
get-new-video-channels-by-rss:
	docker-compose exec monitor-php php artisan get-new-video-channels-by-rss:run
get-video-info:
	docker-compose exec monitor-php php artisan get-video-info:run
gophp:
	docker-compose exec monitor-php bash