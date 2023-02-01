# TalkSpace

**TalkSpace** is an online solution designed to make it easy for patients to schedule therapy sessions.

## Commandes

### Start project

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose up` (the logs will be displayed in the current shell) or Run `docker compose up -d` to run in background
4. Run `npm install` to install javascript dependencies
5. Run `npm run build` to build javascript project
6. Let's go : [`https://localhost:443`](https://localhost:443)

### Stop project

1. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Issue name rules

- **[BUILD]** : _Changes that affect the build system or external dependencies_
- **[CI]** : _Changes to our CI configuration files and scripts_
- **[DOCS]** : _Documentation only changes_
- **[FEATURE]** : _A new feature issue_
- **[BUG]** : _A bug issue_

## Commit message rules

- **build** : _Changes that affect the build system or external dependencies_
- **ci** : _Changes to our CI configuration files and scripts_
- **docs** : _Documentation only changes_
- **feat** : _A new feature_
- **fix** : _A bug fix_
- **perf** : _A code change that improves performance_
- **refactor** : _A code change that neither fixes a bug nor adds a feature_
- **style** : _Changes that do not affect the meaning of the code_
- **test** : _Adding missing tests or correcting existing tests_

## Working process

1. Go to base branch : `git checkout BRANCH_NAME`
2. Create your branch : `git checkout -b YOUR_BRANCH`
3. Add your changes
4. Add the new code to git : `git add .`
5. Commit your changes : `git commit -m "COMMIT_MESSAGE"` (check commit message rules)
6. Push your changes to GitHub : `git push origin YOUR_BRANCH`
7. Create a GitHub pull request : create the PR, assign yourself, choose a label, choose linked issue and if you're done, assing a reviewer
