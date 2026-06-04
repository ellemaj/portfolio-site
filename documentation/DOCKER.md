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