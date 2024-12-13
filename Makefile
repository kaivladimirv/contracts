init: init-env docker-down-clear docker-pull docker-build composer-install docker-up db-create migrate
restart: down up
up: docker-up
down: docker-down

docker-pull:
	docker compose pull

docker-build:
	docker compose build

docker-up:
	make create-directories
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v  --remove-orphans

init-env:
	[ -f .env ] || cp .env.example .env

create-directories:
	mkdir -p ./storages/logs/supervisor
	mkdir -p ./storages/logs/activity
	mkdir -p ./storages/madeline

test:
	docker compose run --rm ins-contracts-php-cli ./vendor/bin/phpunit tests

test-coverage:
	docker compose run --rm ins-contracts-php-cli ./vendor/bin/phpunit tests --coverage-html ./storages/coverage

lint:
	docker compose run --rm ins-contracts-php-cli composer exec --verbose phpcs -- --standard=phpcs.xml

fix-code-style:
	docker compose run --rm ins-contracts-php-cli composer exec --verbose phpcbf -- --standard=phpcs.xml

run-send-email-confirm-token-consumer:
	docker compose run --rm ins-contracts-php-cli php ./public/app.php run-send-email-confirm-token-consumer

run-recalc-balance-consumer:
	docker compose run --rm ins-contracts-php-cli php ./public/app.php run-recalc-balance-consumer

run-balance-notifier-consumer:
	docker compose run --rm ins-contracts-php-cli php ./public/app.php run-balance-notifier-consumer

telegram-login:
	make telegram-logout
	docker compose run --rm --user www-data ins-contracts-php-cli php ./public/app.php telegram-login
	make rerun-supervisor

telegram-logout:
	docker compose run --rm ins-contracts-php-cli php ./public/app.php telegram-logout

rerun-supervisor:
	docker exec ins-contracts-supervisor supervisorctl restart all

db-create:
	docker compose run --rm ins-contracts-php-cli php ./public/app.php db-create

migrate:
	docker compose run --rm ins-contracts-php-cli php ./public/app.php migrate

clear-cache:
	docker compose run --rm ins-contracts-php-cli php ./public/app.php clear-cache

composer-install:
	docker compose run --rm ins-contracts-php-cli composer install

build-apidoc:
	apidoc -i ./src -o ./public/apidoc

composer-validate:
	docker compose run --rm ins-contracts-php-cli composer validate

composer-outdated:
	docker compose run --rm ins-contracts-php-cli composer outdated --direct --major-only --strict

composer-unused:
	docker compose run --rm ins-contracts-php-cli vendor/bin/composer-unused

composer-audit:
	docker compose run --rm ins-contracts-php-cli composer audit

psalm:
	docker compose run --rm ins-contracts-php-cli ./vendor/bin/psalm
