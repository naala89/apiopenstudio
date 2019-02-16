ARGS = $(filter-out $@,$(MAKECMDGOALS))
MAKEFLAGS += --silent

start:
	./.docker/run-proxy.sh
	docker-compose start

up:
	./.docker/run-proxy.sh
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
#############################
# Argument fix workaround
#############################
%:
	@: