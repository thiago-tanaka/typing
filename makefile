ifneq (,$(findstring xterm,${TERM}))
	BLACK        := $(shell tput -Txterm setaf 0)
	RED          := $(shell tput -Txterm setaf 1)
	GREEN        := $(shell tput -Txterm setaf 2)
	YELLOW       := $(shell tput -Txterm setaf 3)
	LIGHTPURPLE  := $(shell tput -Txterm setaf 4)
	PURPLE       := $(shell tput -Txterm setaf 5)
	BLUE         := $(shell tput -Txterm setaf 6)
	WHITE        := $(shell tput -Txterm setaf 7)
	RESET := $(shell tput -Txterm sgr0)
else
	BLACK        := ""
	RED          := ""
	GREEN        := ""
	YELLOW       := ""
	LIGHTPURPLE  := ""
	PURPLE       := ""
	BLUE         := ""
	WHITE        := ""
	RESET        := ""
endif

init_confirmed:
	cp .env.example .env;
	php artisan key:generate;
	docker stop $$(docker ps -a -q);
	cd docker; docker-compose up -d;
	docker exec -it docker_app_1 composer install;
	docker exec -it docker_app_1 php artisan migrate:fresh --seed;
	docker exec -it docker_app_1 npm install;
	docker exec -it docker_app_1 npm run dev;
	sudo chown -R $$USER:www-data storage;
	sudo chown -R $$USER:www-data bootstrap/cache;
	chmod -R 775 storage;
	chmod -R 775 bootstrap/cache

init:
	@echo -n "$(YELLOW) This command will erase all data from the database. Are you sure? [y/N] $(RESET)" && read ans && [ $${ans:-N} = y ] && make init_confirmed

migrate:
	@docker exec -it docker_app_1 php artisan migrate

migrate_rollback:
	docker exec -it docker_app_1 php artisan migrate:rollback

dockerup:
	@cd docker; docker-compose up -d

dockerdown:
	@docker stop $$(docker ps -a -q);

model:
	@php artisan make:model $(model) -c --resource

permissions:
	@sudo chown -R $$USER:www-data storage;sudo chown -R $$USER:www-data bootstrap/cache;chmod -R 775 storage;chmod -R 775 bootstrap/cache

.PHONY: dockerup model permissions migrate migrate_rollback