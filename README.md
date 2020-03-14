# Phalcon API

This is Docker based Phalcon API with structured framework and MySQL 8.0 DB. Build is not perfect and has some bugs, but it works and runs. If you find bug and know how to fix it - contact me.

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
- Sometimes only total clearing of these dirs helps. In Linux you can run `sudo ./data/clear.sh` to do it.