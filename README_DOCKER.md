# Docker Setup for HBD HRM API

This project includes Docker configuration for running the Laravel application with all necessary services.

## Services Included

1. **App Container** - PHP 8.1 FPM application server
2. **Nginx Container** - Web server running on port 8000
3. **MySQL Container** - Database server running on port 3306
4. **Redis Container** - Queue backend running on port 6379
5. **Queue Worker Container** - Processes queued jobs
6. **Horizon Container** - Laravel Horizon for queue monitoring

## Setup Instructions

1. **Build and start the containers:**
   ```bash
   docker-compose up -d
   ```

2. **Install PHP dependencies:**
   ```bash
   docker-compose exec app composer install
   ```

3. **Generate application key:**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

4. **Run database migrations:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

5. **Access the application:**
   - Web application: http://localhost:8000
   - Laravel Horizon (queue monitoring): http://localhost:8000/horizon

## Managing Queues

The application uses Laravel Horizon for queue management. You can monitor and manage queues through the Horizon dashboard.

## Stopping the Services

To stop all services:
```bash
docker-compose down
```

To stop all services and remove volumes:
```bash
docker-compose down -v
