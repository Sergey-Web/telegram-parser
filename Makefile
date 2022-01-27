init: down pull build up composer-install
restart: down up

bash: 
	docker exec -ti notify-parser-php-fpm bash

up:
	docker-compose up -d

down:
	docker-compose down --remove-orphans

pull:
	docker-compose pull

build:
	docker-compose build

test:
	docker-compose exec notify-parser-php-fpm vendor/bin/phpunit

docker-down-clear:
	docker-compose down -v --remove-orphans

composer-install:
	docker-compose run php-fpm composer install