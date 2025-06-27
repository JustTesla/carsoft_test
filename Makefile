build:
	export DOCKER_BUILDKIT=1 && docker-compose -p car_soft -f docker/docker-compose.yml up -d --build --force-recreate

down:
	export DOCKER_BUILDKIT=1 && docker-compose -p car_soft -f docker/docker-compose.yml stop

stage: build-stage

build-stage:
	export DOCKER_BUILDKIT=1 && docker-compose -p car_soft -f docker/docker-compose-stage.yml up -d --build --force-recreate

down-stage:
	export DOCKER_BUILDKIT=1 && docker-compose -p car_soft -f docker/docker-compose-stage.yml stop

prod: build-prod

build-prod:
	export DOCKER_BUILDKIT=1 && docker-compose -p car_soft -f docker/docker-compose-prod.yml up -d --build --force-recreate

down-prod:
	export DOCKER_BUILDKIT=1 && docker-compose -p car_soft -f docker/docker-compose-prod.yml stop