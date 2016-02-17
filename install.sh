#!/bin/bash
#Documentation:
#http://www.freeos.com/guides/lsst/
#http://linuxconfig.org/bash-scripting-tutorial
#http://www.freeos.com/guides/lsst/misc.htm#colorfunandmore

echo -e "\n\033[1m\033[34mWelcome to install script!\033[0m\n"
echo "Choose what you want to do:"
echo " 1 - composer self-update and install"
echo " 2 - npm, bower install"
echo " 3 - gulp run"
echo " 4 - database schema:update"
echo " 5 - load fixtures (dev|test|default:dev)"
echo " 6 - clear cache (prod|dev|test|all|default:all)"
echo " 9 - run all command step by step"
echo " 0 - exit"
echo -e -n "\n\033[1m\033[34m Please choose > \033[0m"

function composer_install
{
    echo -e "\n\033[1m\033[34m Composer install \033[0m"
	composer self-update
	composer install -n
}

function npm_bower_install
{
	echo -e "\n\033[1m\033[34m Npm, bower install \033[0m"
	npm install
	./node_modules/.bin/bower install
}

function gulp_run
{
	echo -e "\n\033[1m\033[34m Gulp run \033[0m"
	./node_modules/.bin/gulp
}

function database_update
{
	echo -e "\n\033[1m\033[34m Updating database \033[0m"
	app/console doctrine:schema:drop --force
	app/console doctrine:schema:update --force
}

function load_fixtures
{
	echo -e "\n\033[1m\033[34m Loading fixtures \033[0m"

	case ${input_argument} in
        test ) echo -e " Used -\033[1m\033[32m test \033[0m"
            app/console doctrine:fixtures:load -n -e test
            ;;
        * ) echo -e " Used -\033[1m\033[32m dev \033[0m"
            app/console doctrine:fixtures:load -n
    esac
}

function clear_cache
{
	echo -e "\n\033[1m\033[34m Clearing caches \033[0m"

	case ${input_argument} in
        prod ) echo -e " Used -\033[1m\033[32m  prod \033[0m"
            app/console cache:clear -e prod
            ;;
        dev ) echo -e " Used -\033[1m\033[32m  dev \033[0m"
            app/console cache:clear -e dev
            ;;
        test ) echo -e " Used -\033[1m\033[32m  test \033[0m"
            app/console cache:clear -e test
            ;;
        * ) echo -e " Used -\033[1m\033[32m all \033[0m"
            app/console cache:clear -e prod
            app/console cache:clear -e dev
            app/console cache:clear -e test
    esac
}

function all_run
{
	echo -e "\n\033[1m\033[34m Run All scripts \033[0m"
	composer_install
	npm_bower_install
	gulp_run
	database_update
	load_fixtures
	clear_cache
}

read input_command input_argument
#echo ${input_command} ${input_argument}

case ${input_command} in
    1 ) composer_install
        ;;
    2 ) npm_bower_install
        ;;
    3 ) gulp_run
        ;;
    4 ) database_update
        ;;
    5 ) load_fixtures
		;;
    6 ) clear_cache
		;;
    9 ) all_run
		;;
    * ) echo -e "\n\033[1m\033[34m Good bye! \033[0m"
esac
