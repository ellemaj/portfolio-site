## Docker

This project can be run using Docker. There are three steps, each building on the previous one.

---

### Step 1 — Single container with SQLite (manual commands)

This step runs the application in a single container using the PHP built-in web server and a SQLite database. Data is stored in a Docker volume so it is not lost when the container stops.

**Build the image:**
```bash
docker build -t maestro .
```

The build step automatically compiles Tailwind CSS — no separate command is needed.

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

The `--build` flag triggers the Dockerfile, which automatically compiles Tailwind CSS.

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

**Start all containers:**
```bash
docker compose up --build -d
```

This starts three containers:
- `app` — the PHP web server
- `db` — the MySQL database (the app waits for it to be healthy before starting)
- `tailwind` — watches `resources/css/app.css` and rebuilds `public/assets/css/app.css` automatically when Twig templates or the CSS source change

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