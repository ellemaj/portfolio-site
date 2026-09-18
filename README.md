## Portfolio website

This is my portfolio website, you can find it on [www.elmarvanloenhout.nl](https://elmarvanloenhout.nl).

Originally, this was an assignment for school (my IT Development Portfolio), so I'm slowly rebuilding it to my own portfolio website. The framework I'm using is custom-made and school-provided: the *maestro* framework. [Original school-provided repository](https://github.com/HZ-ICT1-2526/itdp-ellemaj).

### Author: [ellemaj](https://github.com/ellemaj)

## Documentation

You can find my documentation and explanations in the [/documentation folder](documentation/).

---

## Docker

This project can be run using Docker. There are three steps, each building on the previous one.

---

### Step 1 — Single container with SQLite (manual commands)

This step runs the application in a single container using the PHP built-in web server and a SQLite database. Data is stored in a Docker volume so it is not lost when the container stops.

**Build the image:**
```bash
docker build -t maestro .
```

**Start the container:**
```bash
docker run -d -p 8888:80 -v maestro_data:/var/www/html --name maestro-app maestro
```

**Run the database migration** (only needed once, the first time):
```bash
docker run --rm -v maestro_data:/var/www/html maestro php maestro migrate
```

**Stop the container:**
```bash
docker stop maestro-app
```

The application is now available at [http://localhost:8888](http://localhost:8888).

---

### Step 2 — Single container with Docker Compose

This step uses Docker Compose to start the same container from step 1 with a single command. Create a `docker-compose.yml` with the following content:

```yaml
services:
  app:
    build: .
    ports:
      - "8888:80"
    volumes:
      - app_data:/var/www/html

volumes:
  app_data:
```

**Start the application:**
```bash
docker compose up --build -d
```

**Run the database migration** (only needed once, the first time):
```bash
docker compose exec app php maestro migrate
```

**Stop the application:**
```bash
docker compose down
```

The application is now available at [http://localhost:8888](http://localhost:8888).

---

### Step 3 — Separate database container with MySQL

This step splits the application into two containers: one for the web server and one for the MySQL database. They communicate over a Docker network. The `docker-compose.yml` in this repository is set up for this step.

**Start both containers:**
```bash
docker compose up --build -d
```

The app container will automatically wait for MySQL to be ready before starting.

**Run the database migration** (only needed once, the first time):
```bash
docker compose exec app php maestro migrate
```

**Stop both containers:**
```bash
docker compose down
```

To also delete the database volume (this will erase all data):
```bash
docker compose down -v
```

The application is now available at [http://localhost:8888](http://localhost:8888).

---

---

## Production Deployment (Strato Shared Hosting)

The application is deployed to Strato shared hosting and is available at the configured domain over HTTPS.

### One-time server setup

These steps only need to be done once when setting up the server for the first time.

**1. Set the document root**

Log in to the [Strato control panel](https://www.strato.de/apps/CustomerService), navigate to your domain settings, and set the **start directory (Startverzeichnis)** to `public`. This tells Apache to serve files from the `public/` subfolder of your uploaded project.

**2. Create the database**

In the Strato control panel, go to **MySQL databases** and create a new database. Note the host, database name, username, and password — you will need them in the next step.

**3. Create the `.env` file on the server**

Connect to the server via FTP (using a client such as FileZilla) using your Strato FTP credentials. In the FTP root, create a file named `.env` with the following content:

```
APP_ENV=production
VIEWS_PATH=app/views
APP_URL=https://yourdomain.com
APP_DB=mysql
DB_HOST=<strato-mysql-host>
DB_NAME=<your-database-name>
DB_USER=<your-database-user>
DB_PASS=<your-database-password>
```

Replace each `<...>` placeholder with the values from the Strato control panel. This file is never committed to Git and is not touched by automated deployments.

**4. Run the database migrations**

In the Strato control panel, open **phpMyAdmin** for your database. Select your database, click **SQL**, and run the contents of each file in the `database/mysql/` directory in order:

1. `database/mysql/1_users.sql`
2. `database/mysql/2_posts.sql`
3. `database/mysql/3_courses.sql`
4. `database/mysql/4_profile.sql`

**5. Add FTP credentials to GitHub Actions secrets**

In your GitHub repository go to **Settings → Secrets and variables → Actions** and add three secrets:

| Secret name    | Value                                   |
|----------------|-----------------------------------------|
| `FTP_SERVER`   | Your Strato FTP hostname (e.g. `ssh.strato.de`) |
| `FTP_USERNAME` | Your Strato FTP username                |
| `FTP_PASSWORD` | Your Strato FTP password                |

### Releasing a new version

1. Push your changes to the `main` branch on GitHub.
2. GitHub Actions runs three CI checks in parallel (PHPStan, PHPCS, Deptrac).
3. If all three checks pass, the application is automatically uploaded to Strato via FTP.
4. If any check fails the deployment is blocked and you will see the error in the **Actions** tab of your repository.

There is no manual FTP upload needed for regular releases — push to `main` and the pipeline handles everything.

### CI checks

| Check   | Tool    | Standard / level |
|---------|---------|-----------------|
| Static analysis | PHPStan | Level 8         |
| Coding style    | PHPCS   | PSR-12          |
| Architecture    | Deptrac | See `deptrac.yaml` |

You can run all checks locally (requires Composer dependencies installed):

```bash
vendor/bin/phpstan analyse --no-progress
vendor/bin/phpcs
vendor/bin/deptrac analyse --no-progress
```

---

## Tests

This project uses PHPUnit for automated testing. The tests require the Docker container to be running.

**Run all tests:**
```bash
docker compose exec app vendor/bin/phpunit
```

**Run with code coverage report:**
```bash
docker compose exec app vendor/bin/phpunit --coverage-text
```

A full test plan with all test cases can be found in [documentation/TEST_PLAN.md](documentation/TEST_PLAN.md).
