# Requirements

* PHP 8.2 or higher (tested in PHP 8.5)
* Composer
* Docker (via Laravel Sail)

# Setup Instructions

1. Clone the repository and navigate into the directory.
2. Install the PHP dependencies:

```bash
composer install
```

3. Create your environment file(After that check correct credential for DB_USERNAME and DB_PASSWORD variables):

```bash
cp .env.example .env
```

4. Start the Docker containers using Laravel Sail. This will set up the application environment along with a MySQL 8.4 database:

```bash
./vendor/bin/sail up -d
```

5. Generate the application key and run database migrations:

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```


# API Endpoints

The API is structured around four primary endpoints:

* `GET /api/guide/{channel_nr}/{date}` — Fetches the complete guide for a specified channel on a specific date.
* `GET /api/on-air/{channel_nr}` — Retrieves the program currently airing for the given channel.
* `GET /api/upcoming/{channel_nr}` — Provides a list of upcoming programs for the specified channel.
* `POST /api/guide` — Stores a new TV guide entry in the system.

  **Headers:**
  ```
  Accept: application/json
  ```

  **Request Body (JSON):**
  ```json
  {
      "title": "test",
      "channel_nr": 1,
      "starts_at": "2026-02-19 21:40:00",
      "ends_at": "2026-02-19 22:40:00"
  }
  ```

# Testing

You can run the application's automated tests using the predefined Composer script or Artisan directly:

```bash
./vendor/bin/sail artisan test
```

# Code Style

You can fix code style issues automatically using Laravel Pint:

```bash
./vendor/bin/pint
```