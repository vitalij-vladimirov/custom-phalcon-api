# Phalcon API

This is Docker based Phalcon API with structured framework and MySQL 8.0 DB.

#### API includes:

- Dockerfile from `webdevops/php-nginx:7.4`
- docker-compose v. 3.7
- PHP 7.4 + nginx
- Composer 1.10.6
- Phalcon 4.0.5
- MySQL 8.0

#### Run application

- `./run up`
  - Runs `docker-compose up -d` (builds new image if now found).
  - Fixes difference of Docker EOL configuration between Windows and Unix/Mac.
  - Runs migrations and seeds.

#### Stop application

- `./run down`
  - Runs `docker-compose down` (just a shorter way to do it).

#### Build/rebuild application

- `./run build`
  - Runs `docker-compose up --build -d` (builds bew image and starts application).
  - Fixes difference of Docker EOL configuration between Windows and Unix/Mac.
  - Runs migrations and seeds.
  - **NOTE:** In Linux `sudo ./run clear` must be ran before rebuild to clear ./data folder.

#### Enter application

- `./run exec`
  - Runs `docker exec -ti $APP bash` (just a shorter way to do it).

#### Display list of running containers

- `./run ps`
  - Runs `docker ps -a` (just a shorter way to do it).

#### Clear ./data folder

- `./run clear` OR `sudo ./run clear` in Linux
  - Removes development database data.

#### Other commands

| Action | Outside container | Inside container |
| --- | --- | --- |
| Run CLI command | `./run cli $module:$task:$command ...$arguments` | `cli $module:$task:$command ...$arguments` |
| Check if DB is online | `./run db` | - |
| Run full test (cs && unit) | `./run test` | - |
| Run Code Standard tests | `./run cs` | `cs` |
| Run code autofix | `./run cs fix` | `cs fix` |
| Run Code Standard in exact directory | `./run cs $dir` | `cs $dir` |
| Run paratest | `./run unit` | `unit` |
| Run exact test class or test method | `./run unit $testName` | `unit $testName` |
| Fix unix files when running docker from windows | `./run fix` | - |

#### If DB does not start

- DB files are saved in `./data/mysql` and mysql logs in `./data/log`.
- In Linux distributions you may need to `chmod 0777` ./data directory.
- When rebuilding image in Linux, total clearing of these dirs has to be done. Run `sudo ./run clear` to do it.

#### Tested on

- Linux (native docker) - most development process done on Linux
  - App URI: `http://localhost:801`.
  - Add 0777 permissions to ./data folder.
  - When new migration or seed created, run `sudo chown $USER ./app/db/(migrations|seeds)/{new_file}.php` or `sudo chown -R $USER ./app/db`.
  - Remove ./data folder content before image rebuild.
- Mac (Docker for Mac)
  - App URI: `http://localhost:801`.
  - No serious problems/advices detected expect that mac is pretty shi**y OS for development.
- Windows (Docker Toolbox only, not tested on Docker for Windows yet)
  - App URI: `http://192.168.99.100:801`
  - If default uri does not work, check docker ip with `docker-machine ip default` command.
  - Use `./run` commands instead of standard `docker-compose` commands since `./run` commands solves some multi platform issues and differences between Unix and Windows Docker configurations.
  - There is a known problem in difference of ending of line (EOL) between Linux and Windows. When running with `./run up` command list of Linux based files are additionally updated if host os is Windows, in other case nothing will work and build will fail.
  - `cs` is configured to change default EOL configuration to use "\r\n" if running in Windows, in other case you will get EOL error in all checked files.
  - Use 'Docker Toolbox Terminal', 'Windows PowerShell' or 'GIT bash' to work with application. DO NOT USE Windows 'cmd'. Just don't do it.
  - If you prefer PHP Storm Terminal, configure it to use one of offered above terminals: `File > Settings > Tools > Terminal > Shell path > $pathToChoosenTerminal`.
  - There should be no problems when running app in 'Docker for Windows', but I couldn't test this platform yet so some unknown problems may occur. Be aware of that.