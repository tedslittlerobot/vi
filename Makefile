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

scrutinizer-prepare:
	@mkdir -p ~/artifacts/coverage

scrutinizer-test: scrutinizer-prepare
	@./vendor/bin/phpunit -v --testsuite "$(SUITE)" --coverage-clover="~/artifacts/coverage/$(SUITE).xml"
	@.echo "Written clover file to ~/artifacts/coverage/$(SUITE).xml"

coverage:
	@./vendor/bin/phpunit --coverage-html $(REPORT_DIR)

report:
	@open $(REPORT_DIR)/index.html

covreport: coverage report
