.SILENT:
.PHONY: help
.DEFAULT_GOAL= help

include .env
export

#COLORS
GREEN  := $(shell tput -Txterm setaf 2)
WHITE  := $(shell tput -Txterm setaf 7)
YELLOW := $(shell tput -Txterm setaf 3)
RESET  := $(shell tput -Txterm sgr0)

# Help function for Makefile

# Add the following 'help' target to your Makefile
# And add help text after each target name starting with '\#\#'
# A category can be added with @category
HELP_FUN = \
    %help; \
    while(<>) { push @{$$help{$$2 // 'options'}}, [$$1, $$3] if /^([a-zA-Z\-.:%]+)\s*:.*\#\#(?:@([a-zA-Z\-]+))?\s(.*)$$/ }; \
    print "usage: make [target]\n\n"; \
    for (sort keys %help) { \
    print "${WHITE}$$_:${RESET}\n"; \
    for (@{$$help{$$_}}) { \
    $$sep = " " x (22 - length $$_->[0]); \
    print "  ${YELLOW}$$_->[0]${RESET}$$sep${GREEN}$$_->[1]${RESET}\n"; \
    }; \
	print ""; }

help: ##@Help Show this help.
	@perl -e '$(HELP_FUN)' $(MAKEFILE_LIST)

phpunit: ##@Test Running PHPUnit Tests
	php bin/phpunit >> phpunit.txt

phpstan: ##@Test Running phpstan Tests
	if [ ! -d $(FILE_TEST) ]; then mkdir $(FILE_TEST); fi;
	if [ -f $(FILE_TEST)/phpstan.txt ]; then rm $(FILE_TEST)/phpstan.txt; fi;
	vendor/bin/phpstan analyse --configuration phpstan-config.neon >> $(FILE_TEST)/phpstan.txt

phpcsfixer: ##@Test Running phpcsfixer
	php /usr/local/bin/php-cs-fixer fix --allow-risky=yes

phploc: ##@Test Running phploc
	if [ ! -d $(FILE_TEST) ];then mkdir $(FILE_TEST); fi;
	if [ -f $(FILE_TEST)/phploc.txt ]; then rm $(FILE_TEST)/phploc.txt; fi;
	php phploc.phar config src tests public >> $(FILE_TEST)/phploc.txt

phpmd: ##@Test Running phpmd
	if [ ! -d $(FILE_TEST) ];then mkdir $(FILE_TEST); fi;
	if [ -f $(FILE_TEST)/phpmd.txt ]; then rm $(FILE_TEST)/phpmd.txt; fi;
	vendor/bin/phpmd config/ src/Controller/ tests/ public/ text phpmd.xml >> $(FILE_TEST)/phpmd.txt

rebuild: ##@Encore Encore rebuild
	./node_modules/.bin/encore dev

watch: ##@Encore Encore rebuild --watch
	./node_modules/.bin/encore dev --watch

cache: ##@Symfony vide le cache
	php bin/console cache:clear

article: ##@Application Créer contenu pour la partie Blog (params int : 1 à 100)
	php bin/console app:create-content $(int)

delarticle: ##@Application Supprime tous les articles de la partie blog
	php bin/console app:delete-content