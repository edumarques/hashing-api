.PHONY: test

php-container="php"

help: ## Shows this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(firstword $(MAKEFILE_LIST)) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'


build: ## Build all docker images from docker-compose.yml
	docker-compose up --build

build-d: ## Detached mode: Build all docker images from docker-compose.yml
	docker-compose up -d --build

up: ## Lift containers already built
	docker-compose up

down-up: ## Lift containers already built (down first)
	docker-compose down && docker-compose up

up-d: ## Detached mode: Run containers in the background, print new container names
	docker-compose up -d

down-up-d: ## Detached mode: Run containers in the background, print new container names (down first)
	docker-compose down && docker-compose up -d

ps: ## Check containers statuses
	docker-compose ps

recreate: ## Recreate containers even if their configuration and image haven't changed
	docker-compose up --force-recreate

down-recreate: ## Recreate containers even if their configuration and image haven't changed (down first)
	docker-compose down && docker-compose up --force-recreate

down: ## Stop containers and removes containers, networks, volumes, and images created by up.
	docker-compose down

remove-orphans: ## Remove containers for services not defined in the docker-compose file
	docker-compose down --remove-orphans

migrate: ## Run application migrations
	docker-compose exec ${php-container} bin/console doctrine:migrations:migrate

migrations-diff: ## Run migrations diff
	docker-compose exec ${php-container} bin/console doctrine:migrations:diff

insights: ## Run PHP insights
	docker-compose exec ${php-container} composer insights

insights-fix: ## Run PHP insights fix
	docker-compose exec ${php-container} composer insights-fix

test: ## Run tests
	docker-compose exec ${php-container} composer test

test-cov: ## Run tests and generate coverage report
	docker-compose exec ${php-container} composer test-coverage
