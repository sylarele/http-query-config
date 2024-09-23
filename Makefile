
SOURCE_DIR = `pwd`
BIN_DIR = ${SOURCE_DIR}/vendor/bin
APP_DIR = ${SOURCE_DIR}/src

define printSection
	@printf "\033[36m\n==================================================\n\033[0m"
	@printf "\033[36m $1 \033[0m"
	@printf "\033[36m\n==================================================\n\033[0m"
endef

#  _   _      _
# | | | |    | |
# | |_| | ___| |_ __
# |  _  |/ _ \ | '_ \
# | | | |  __/ | |_) |
# \_| |_/\___|_| .__/
#              | |
#              |_|

.PHONY: help
help: ## Liste les commandes présentes
	$(call printSection,HELP)
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) \
	| sort \
	| awk 'BEGIN {FS = ":.*?## "}; {printf "${_GREEN}%-20s${_END} %s\n", $$1, $$2}' \
	| sed -e 's/##//'

#  _____            _
# /  __ \          | |
# | /  \/ __ _  ___| |__   ___
# | |    / _` |/ __| '_ \ / _ \
# | \__/\ (_| | (__| | | |  __/
# \____/\__,_|\___|_| |_|\___|

.PHONY: clear-phpstan
clear-phpstan:  ## Vide les caches et relance phpstan
	$(call printSection,CLEAR CACHE PHPSTAN)
	rm -R ${SOURCE_DIR}/storage/tmp/phpstan
	make phpstan

#  _____             _ _ _
# |  _  |           | (_) |
# | | | |_   _  __ _| |_| |_ _   _
# | | | | | | |/ _` | | | __| | | |
# \ \/' / |_| | (_| | | | |_| |_| |
#  \_/\_\\__,_|\__,_|_|_|\__|\__, |
#                             __/ |
#                            |___/

.PHONY: phpstan
phpstan:  ## Lance l'analyse de code
	$(call printSection,PHPSTAN)
	${BIN_DIR}/phpstan.phar analyse -c phpstan.neon.dist --memory-limit=1G

.PHONY: rector
rector: ## Lance l'analyse de la refactorisation de la base de code
	$(call printSection,RECTOR)
	${BIN_DIR}/rector process --dry-run

.PHONY: rector-process
rector-process: ## Lance la refactorisation de la base de code
	$(call printSection,RECTOR)
	${BIN_DIR}/rector process

.PHONY: fix
fix:  ## Lance le formatage du code
	$(call printSection,DUSTER)
	${BIN_DIR}/php-cs-fixer fix

.PHONY: dependencies
dependencies: ## Check if the dependency are compliant
	$(call printSection,COMPOSER DEPENDENCY)
	${BIN_DIR}/composer-dependency-analyser \
		--ignore-shadow-deps \
		--ignore-unused-deps

.PHONY: migrate-fresh
migrate-fresh:  ## Lance le formatage du code
	$(call printSection,Migrate fresh)
	${BIN_DIR}/testbench migrate:fresh

.PHONY: test
test:  ## Lance le formatage du code
	$(call printSection,DUSTER)
	${BIN_DIR}/phpunit
