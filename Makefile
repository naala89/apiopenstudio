ARGS = $(filter-out $@,$(MAKECMDGOALS))
MAKEFLAGS += --silent
start:
	./.docker/run-proxy.sh
	docker-compose start
up:
	./.docker/run-proxy.sh
	docker-compose up -d
down:
	docker-compose down
#############################
# Argument fix workaround
#############################
%:
	@: