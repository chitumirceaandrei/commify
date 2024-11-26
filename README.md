# Laravel Tax Calculator

## Summary

This application is a UK Income Tax Calculator built using the Laravel framework. It calculates the income tax based on predefined tax bands and provides the net annual and monthly salary after tax deductions. The application is designed to be scalable and maintainable, following best practices in object-oriented design and software engineering principles.

## Prerequisites

- Docker
- Docker Compose

## Getting Started

### Step 1: Clone the Repository

```sh
git clone https://github.com/chitumirceaandrei/commify.git commify
cd commify
```

### Step 2: Set Up Environment Variables
Copy the .env.example file to .env and update the necessary environment variables.

```sh
cp .env.example .env
```
Replace the DB credentials with the following:
```
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=admin
DB_PASSWORD=admin
DB_ROOT_PASSWORD=root
```

### Step 3: Build and Start Docker Containers
From root folder run the following command to build and start the Docker containers(It may take 5-6 minutes):

```sh
docker compose -f deploy/docker-compose.yml --env-file ./.env up --build
```

### Step 4: Run the Tests
You have two options to do this:
1. Directly from docker desktop container - run `php artisan test`
2. From root folder run `docker exec -t laravelapp php artisan test`

### Application Overview

### Features

* Income Tax Calculation: Calculates the income tax based on predefined tax bands.
* Net Salary Calculation: Provides the net annual and monthly salary after tax deductions.
* User Interface: Simple UI to enter the gross annual salary and view the calculation results.

### Tax Bands
The application uses the following tax bands for calculation:

* Tax Band A: £0 - £5000 at 0%
* Tax Band B: £5000 - £20000 at 20%
* Tax Band C: £20000 and above at 40%

### Directory Structure

* app/Http: Contains the controllers.
* app/Models: Contains the Eloquent models.
* app/Providers: Contains the service providers.
* app/Repositories: Contains the repository interfaces and implementations.
* app/Services: Contains the service classes.
* database/migrations: Contains the database migration files.
* database/seeders: Contains the database seeder files.
* resources/views: Contains the Blade templates.
* routes: Contains the route definitions.
* tests: Contains the test files.

