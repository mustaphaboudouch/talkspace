# TalkSpace

**TalkSpace** is an online solution designed to make it easy for patients to schedule therapy sessions.

## Commandes

### Run project

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose up` (the logs will be displayed in the current shell) or Run `docker compose up -d` to run in background
4. Let's go 🚀 `https://localhost:443`

### Stop project

1. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Commits rules

- **build** : _Changes that affect the build system or external dependencies_
- **ci** : _Changes to our CI configuration files and scripts_
- **docs** : _Documentation only changes_
- **feat** : _A new feature_
- **fix** : _A bug fix_
- **perf** : _A code change that improves performance_
- **refactor** : _A code change that neither fixes a bug nor adds a feature_
- **style** : _Changes that do not affect the meaning of the code_
- **test** : _Adding missing tests or correcting existing tests_
