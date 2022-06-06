.SILENT:
.PHONY: all install uninstall clean dist
.DEFAULT_GOAL= help

include .env

.PHONY: help

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

test: ##@Test Running PHPUnit Tests
	php bin/phpunit

rebuild: ##@Encore Encore rebuild
	./node_modules/.bin/encore dev

watch: ##@Encore Encore rebuild --watch
	./node_modules/.bin/encore dev --watch

cache: ##@Symfony vide le cache
	php bin/console cache:clear

article: ##@Application Créer contenu pour la partie Blog
	php bin/console app:create-content $(int)

delarticle: ##@Application Supprime tous les articles de la partie blog
	php bin/console app:delete-content