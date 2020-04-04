# Phalcon API

This is Docker based Phalcon API with structured framework and MySQL 8.0 DB.

#### API includes:

- Dockerfile from `webdevops/php-nginx:7.4`
- docker-compose v. 3.7
- PHP 7.4 + nginx
- Composer 1.9.3
- Phalcon 4.0.4
- MySQL 8.0

#### First run

- Configure (optional):
  - copy `./.config/.env.development` to `./.env` and change configuration to preferred
  - change app and db titles in `./docker-compose.yml`
  - change db configuration in `./docker-compose.yml`, `./.config/.env.development` and `./.config/mysql/my.cnf` files
- Run:
  - `docker-compose up --build`

#### Next runs

- `docker-compose up -d`

#### If DB does not start

- DB files are saved in `./data/mysql` and mysql logs in `./data/log`.
- In Linux distributions you have to `chmod 0777` both dirs.
- When rebuilding image, total clearing of these dirs has to be done. In Linux you can run `sudo ./data/clear.sh` to do it.

#### PHP Storm IDE templates

**If you use PHP Storm you can import these settings to automate:**
- Routes creation
- Controller creation
- Service creation
- Task creation
- Getters and Setters creation

**Notes:**
- Only templates will be imported. All other settings should stay the same.
- I suggest you to create current PHP Storm settings backup before importing my examples.

#### Running PHP Unit and Paratest

* Paratest (full PHP unit testing): run `unit`
 anywhere (works same as running `vendor/bin/paratest`)
* Filtered testing: run `unit TestClassOrMethodName` (works same as `vendor/bin/phpunit --filter TestClassOrMethodName`)
* Running PHP unit with command: `unit --command arguments` (works same as `vendor/bin/phpunit --command arguments`)

#### Running Code Standart testing

* Run `cs` from anywhere to run full `vendor/bin/php-cs-fixer` and `vendor/bin/phpcs` testing for directories `mvc` and `modules`