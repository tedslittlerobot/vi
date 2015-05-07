REPORT_DIR=./report
SUITE=unit

install:
	@composer update --prefer-dist

clean:
	@rm -rf vendor

reset:
	@composer update --no-scripts

test:
	@./vendor/bin/phpunit -v --testsuite "$(SUITE)"

scrutinizer-test:
	@./vendor/bin/phpunit -v --testsuite "$(SUITE)" --coverage-clover="~/artifacts/coverage/$(SUITE).xml"

coverage:
	@./vendor/bin/phpunit --coverage-html $(REPORT_DIR)

report:
	@open $(REPORT_DIR)/index.html

covreport: coverage report
