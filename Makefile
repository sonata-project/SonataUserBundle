test:
	php vendor/bin/phpunit -c phpunit.xml.dist
	cd Resources/doc && sphinx-build -W -b html -d _build/doctrees . _build/html
