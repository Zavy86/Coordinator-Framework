#
# Makefile
#
# for development environment make dev
#

dev:
	docker-compose -f docker/docker-compose.yml -p coordinator-framework down
	docker-compose -f docker/docker-compose.yml -p coordinator-framework rm -f
	docker-compose -f docker/docker-compose.yml -p coordinator-framework build --no-cache
	docker-compose -f docker/docker-compose.yml -p coordinator-framework up -d --remove-orphans
	docker image prune -f --filter="dangling=true"
