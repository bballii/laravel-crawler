# Laravel Web Crawler Application

A Laravel-based web crawler application that extracts HTML elements containing specified keywords from websites. The application provides both a web interface and REST API for crawling operations, with user authentication, API key management, and comprehensive crawler history tracking.

## Features

- **Web Crawling**: Extract HTML elements containing specified keywords from any website
- **User Authentication**: Secure login system with role-based access control (regular users and super admins)
- **API Access**: RESTful API with support for both API key and Sanctum token authentication
- **API Key Management**: Create, manage, and revoke API keys with expiration support
- **Crawler History**: Track all crawling operations with execution metrics (super admin only)
- **Queue Management**: Laravel Horizon integration for background job processing
- **Modern UI**: Responsive web interface built with Tailwind CSS and Vite

## Technology Stack

- **Backend**: Laravel 12.x (PHP 8.2+)
- **Database**: MySQL 8.0
- **Cache/Queue**: Redis 7
- **Queue Dashboard**: Laravel Horizon
- **Frontend**: Tailwind CSS, Vite, Axios
- **Authentication**: Laravel Sanctum
- **Containerization**: Docker & Docker Compose
- **Web Server**: Nginx

## Requirements

### For Local Development
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0 or higher
- Redis 7 or higher

### For Docker Deployment
- Docker 20.10 or later
- Docker Compose 2.0 or later
- At least 2GB of free disk space

## Installation

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd crawler-app
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install frontend dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Update `.env` file** with your database and Redis configuration:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=crawler
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379

   QUEUE_CONNECTION=redis
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   ```

8. **Start Laravel Horizon** (in a separate terminal)
   ```bash
   php artisan horizon
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

The application will be available at `http://localhost:8000`.

### Docker Deployment

For detailed Docker deployment instructions, refer to the [DEPLOYMENT.md](./DOCX/DEPLOYMENT.md) file.

**Quick Start:**
```bash
# Create .env file
cp .env.example .env

# Generate application key
docker compose run --rm app php artisan key:generate

# Build and start containers
docker compose build
docker compose up -d

# Run migrations
docker compose exec app php artisan migrate --force

# Create storage link
docker compose exec app php artisan storage:link
```

## Application Architecture

### Directory Structure

```
app/
├── Console/Commands/     # Artisan commands
├── Http/
│   ├── Controllers/      # Application controllers
│   ├── Middleware/       # Custom middleware
│   └── Requests/         # Form request validation
├── Models/               # Eloquent models
└── Providers/            # Service providers

database/
├── migrations/           # Database migrations
└── seeders/              # Database seeders

resources/
├── css/                  # Stylesheets
├── js/                   # JavaScript files
└── views/                # Blade templates

routes/
├── api.php               # API routes
└── web.php               # Web routes
```

### Key Components

#### Models

- **User**: User authentication and authorization with role-based access
- **ApiKey**: API key management with SHA-256 hashing and expiration support
- **CrawlerHistory**: Tracks all crawling operations with execution metrics

#### Controllers

- **CrawlerController**: Handles web crawling operations via web and API
- **ApiKeyController**: Manages API key CRUD operations
- **DashboardController**: Main dashboard and crawler interface
- **CrawlerHistoryController**: Displays crawler history (super admin only)
- **ProfileController**: User profile management

#### Middleware

- **api.key**: Validates API key authentication for API requests
- **superadmin**: Restricts access to super admin routes

## API Documentation

### Authentication

The API supports two authentication methods:

1. **API Key Authentication**: Include API key in header or query parameter
2. **Sanctum Token Authentication**: Bearer token authentication

### Endpoints

#### Start Crawler

**Endpoint**: `POST /api/crawler`

**Authentication**: API Key or Sanctum Token

**Headers**:
```
X-API-Key: sk_your_api_key_here
```
or
```
Authorization: Bearer {sanctum_token}
```

**Request Body**:
```json
{
  "site": "https://example.com",
  "keywords": "keyword1, keyword2, keyword3"
}
```

**Response** (Success):
```json
{
  "status": "success",
  "matches": [
    {
      "element": "<div>...</div>",
      "tag": "div",
      "attributes": {...}
    }
  ],
  "matches_count": 5,
  "execution_time": 1234
}
```

**Response** (Error):
```json
{
  "message": "Error message",
  "status": "error"
}
```

### API Key Management

API keys can be managed through the web interface at `/api-keys`. Each API key:
- Is prefixed with `sk_` and contains 48 random characters
- Is hashed using SHA-256 before storage
- Can have an optional expiration date
- Tracks last usage timestamp

## Web Interface

### Routes

- `/` - Redirects to dashboard or login
- `/login` - User login
- `/dashboard` - Main dashboard (authenticated)
- `/crawler` - Web crawler interface (authenticated)
- `/api-keys` - API key management (authenticated)
- `/profile` - User profile management (authenticated)
- `/crawler-history` - Crawler history (super admin only)
- `/horizon` - Laravel Horizon dashboard

### User Roles

- **Regular User**: Can use crawler, manage API keys, and view own profile
- **Super Admin**: All regular user permissions plus access to crawler history

## Queue Processing

The application uses Laravel Horizon for queue management. All crawling operations are processed through Redis queues for better performance and scalability.

**Access Horizon Dashboard**: `http://localhost/horizon`

## Development

### Running Tests

```bash
php artisan test
```

### Code Style

The project uses Laravel Pint for code formatting:

```bash
./vendor/bin/pint
```

### Frontend Development

For frontend development with hot reload:

```bash
npm run dev
```

### Database Migrations

Create a new migration:
```bash
php artisan make:migration create_table_name
```

Run migrations:
```bash
php artisan migrate
```

Rollback last migration:
```bash
php artisan migrate:rollback
```

## Configuration

### Environment Variables

Key environment variables in `.env`:

```env
APP_NAME="Crawler App"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crawler
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis

HORIZON_PATH=horizon
```

### Cache Configuration

The application uses Redis for caching and session storage. Ensure Redis is running and properly configured in your `.env` file.

## Troubleshooting

### Common Issues

**Database Connection Error**
- Verify MySQL is running
- Check database credentials in `.env`
- Ensure database exists

**Redis Connection Error**
- Verify Redis is running
- Check Redis configuration in `.env`
- Test connection: `redis-cli ping`

**Permission Errors**
- Ensure storage and bootstrap/cache directories are writable:
  ```bash
  chmod -R 775 storage bootstrap/cache
  ```

**Queue Jobs Not Processing**
- Ensure Laravel Horizon is running: `php artisan horizon`
- Check Redis connection
- Verify queue configuration in `.env`

## Security Considerations

- API keys are hashed using SHA-256 before storage
- Passwords are hashed using bcrypt
- SSL verification can be disabled for crawling (configured in CrawlerController)
- Use HTTPS in production
- Regularly rotate API keys
- Set appropriate expiration dates for API keys

## License

This project is open-sourced software licensed under the MIT license.

## Additional Documentation

- [Deployment Guide](./DOCX/DEPLOYMENT.md) - Comprehensive Docker deployment instructions
- [Docker Quick Reference](./DOCX/DOCKER_README.md) - Quick Docker deployment reference
