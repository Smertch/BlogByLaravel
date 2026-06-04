.DEFAULT_GOAL := help
SHELL := /bin/bash

PHP        ?= php
COMPOSER   ?= composer
ARTISAN    ?= $(PHP) artisan
COMPOSE    ?= docker-compose
DC_PHP     ?= $(COMPOSE) exec -T php-fpm

.PHONY: help install up down build logs ps shell migrate \
        worker test lint lint-fix phpstan ci clean

help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-18s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Install PHP dependencies
	$(COMPOSER) install --prefer-dist --no-interaction --no-progress

up: ## Start the docker stack
	$(COMPOSE) up -d

down: ## Stop the docker stack
	$(COMPOSE) down

build: ## Rebuild the docker images
	$(COMPOSE) build --pull

logs: ## Tail docker logs
	$(COMPOSE) logs -f --tail=200

ps: ## Show docker containers
	$(COMPOSE) ps

shell: ## Enter the php-fpm container
	$(DC_PHP) bash

migrate: ## Run Laravel migrations
	$(DC_PHP) $(ARTISAN) migrate --force

worker: ## Start the queue worker
	$(DC_PHP) $(ARTISAN) queue:work rabbitmq --tries=3

test: ## Run PHPUnit
	$(DC_PHP) vendor/bin/phpunit --colors=always

lint: ## Run Laravel Pint (dry-run)
	$(DC_PHP) vendor/bin/pint --test

lint-fix: ## Apply Pint fixes
	$(DC_PHP) vendor/bin/pint

phpstan: ## Run PHPStan static analysis
	$(DC_PHP) vendor/bin/phpstan analyse --no-progress

ci: composer-validate lint phpstan test ## Run the full local CI suite

composer-validate: ## Validate composer.json
	$(COMPOSER) validate --strict --no-check-publish

clean: ## Remove caches and logs
	$(DC_PHP) $(ARTISAN) optimize:clear
	rm -rf storage/logs/*.log
