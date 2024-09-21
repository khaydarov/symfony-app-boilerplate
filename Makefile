### Application ###

cc:
	${DOCKER_COMPOSE_PHP_FPM_EXEC} bin/console cache:clear

cs_fix:
	${DOCKER_COMPOSE_PHP_FPM_EXEC} vendor/bin/php-cs-fixer fix