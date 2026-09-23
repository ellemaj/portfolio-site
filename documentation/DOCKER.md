## Docker

This project runs using Docker Compose, with a separate container for the web server and one for the MySQL database. They communicate over a Docker network. The `docker-compose.yml` in this repository is set up for this.

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
