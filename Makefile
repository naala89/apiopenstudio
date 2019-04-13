ARGS = $(filter-out $@,$(MAKECMDGOALS))
MAKEFLAGS += --silent

start:
	docker-compose start

up:
	docker-compose up -d --remove-orphans
	
down: stop

stop:
	@echo "Stopping containers for GaterData..."
	@docker-compose stop

prune:
	@echo "Removing containers for GaterData..."
	@docker-compose down -v

logs:
	@docker-compose logs -f $(filter-out $@,$(MAKECMDGOALS))

admin-shell:
	docker exec -ti -e COLUMNS=$(shell tput cols) -e LINES=$(shell tput lines) $(shell docker ps --filter name='gaterdata-admin' --format "{{ .ID }}") sh

api-shell:
	docker exec -ti -e COLUMNS=$(shell tput cols) -e LINES=$(shell tput lines) $(shell docker ps --filter name='gaterdata-api' --format "{{ .ID }}") sh

#############################
# Argument fix workaround
#############################
%:
	@:
