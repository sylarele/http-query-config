
SOURCE_DIR = `pwd`
BIN_DIR = ${SOURCE_DIR}/vendor/bin
APP_DIR = ${SOURCE_DIR}/src

define printSection
	@printf "\033[36m\n==================================================\n\033[0m"
	@printf "\033[36m $1 \033[0m"
	@printf "\033[36m\n==================================================\n\033[0m"
endef

.PHONY: all ## Run all checks
all: fix phpstan rector dependencies archi test

#  _   _      _
# | | | |    | |
# | |_| | ___| |_ __
# |  _  |/ _ \ | '_ \
# | | | |  __/ | |_) |
# \_| |_/\___|_| .__/
#              | |
#              |_|

.PHONY: help
help: ## List available commands
	$(call printSection,HELP)
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) \
	| sort \
	| awk 'BEGIN {FS = ":.*?## "}; {printf "${_GREEN}%-20s${_END} %s\n", $$1, $$2}' \
	| sed -e 's/##//'

.PHONY: migrate-fresh
migrate-fresh: ## Run database migration
	$(call printSection,Migrate fresh)
	${BIN_DIR}/testbench migrate:fresh

#  _____            _
# /  __ \          | |
# | /  \/ __ _  ___| |__   ___
# | |    / _` |/ __| '_ \ / _ \
# | \__/\ (_| | (__| | | |  __/
# \____/\__,_|\___|_| |_|\___|

.PHONY: clear-cache
clear-cache: ## Clear caches
	$(call printSection,CLEAR CACHE)
	rm -R ${SOURCE_DIR}/storage/tmp

#  _____             _ _ _
# |  _  |           | (_) |
# | | | |_   _  __ _| |_| |_ _   _
# | | | | | | |/ _` | | | __| | | |
# \ \/' / |_| | (_| | | | |_| |_| |
#  \_/\_\\__,_|\__,_|_|_|\__|\__, |
#                             __/ |
#                            |___/

.PHONY: phpstan
phpstan: ## Run code analysis
	$(call printSection,PHPSTAN)
	${BIN_DIR}/phpstan.phar analyse -c phpstan.neon.dist --memory-limit=1G

.PHONY: rector
rector: ## Run code base refactoring analysis
	$(call printSection,RECTOR)
	${BIN_DIR}/rector process --dry-run

.PHONY: rector-process
rector-process: ## Run code base refactoring
	$(call printSection,RECTOR)
	${BIN_DIR}/rector process

.PHONY: fix
fix: ## Run the code formatting analysis
	$(call printSection,PHP-CS-FIXER)
	${BIN_DIR}/php-cs-fixer fix --dry-run

.PHONY: fix-process
fix-process: ## Run code formatting
	$(call printSection,PHP-CS-FIXER DRY RUN)
	${BIN_DIR}/php-cs-fixer fix

.PHONY: dependencies
dependencies: ## Check if the dependency are compliant
	$(call printSection,COMPOSER DEPENDENCY)
	${BIN_DIR}/composer-dependency-analyser

#  _____         _
# |_   _|       | |
#   | | ___  ___| |_
#   | |/ _ \/ __| __|
#   | |  __/\__ \ |_
#   \_/\___||___/\__|

.PHONY: test
test: ## Run unit and feature tests
	$(call printSection,TEST PHPUNIT)
	${BIN_DIR}/phpunit $(args)

.PHONY: archi
archi: ## Run archi tests
	$(call printSection,TEST STRUCTURA)
	${BIN_DIR}/structura $(args)
