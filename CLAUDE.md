# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 11 blog application with PHP 8.4, containerized with Docker for AWS EC2 deployment. The application includes user authentication and a complete blog post CRUD system.

## Common Commands

### Docker Commands
- `docker-compose up -d --build` - Build and start all containers
- `docker-compose down` - Stop all containers
- `docker-compose logs -f app` - View application logs
- `docker-compose exec app bash` - Access app container shell

### Laravel Commands (run inside container)
- `docker-compose exec app php artisan migrate` - Run database migrations
- `docker-compose exec app php artisan key:generate` - Generate application key
- `docker-compose exec app php artisan cache:clear` - Clear all caches
- `docker-compose exec app php artisan config:cache` - Cache configuration
- `docker-compose exec app php artisan route:cache` - Cache routes
- `docker-compose exec app composer install` - Install PHP dependencies

### Development Workflow
1. Start containers: `docker-compose up -d`
2. Run migrations: `docker-compose exec app php artisan migrate`
3. Access application at `http://localhost:8000`

## Architecture

### Key Components
- **Authentication System**: Located in `app/Http/Controllers/Auth/`
  - LoginController - Handles user login/logout
  - RegisterController - Handles user registration
  
- **Blog System**: 
  - Post model (`app/Models/Post.php`) - Blog post entity
  - PostController (`app/Http/Controllers/PostController.php`) - CRUD operations
  - PostPolicy (`app/Policies/PostPolicy.php`) - Authorization rules
  
- **Views**: Blade templates in `resources/views/`
  - layouts/app.blade.php - Main layout with navigation
  - auth/* - Login and registration views
  - posts/* - Blog post views (index, show, create, edit)
  - dashboard.blade.php - User dashboard

### Database Structure
- **users** - User accounts
- **posts** - Blog posts with title, content, user_id, published_at
- **sessions** - User sessions
- **cache** - Application cache

### Docker Configuration
- **Dockerfile** - PHP 8.4-FPM with Nginx and Supervisor
- **docker-compose.yaml** - Orchestrates app and MySQL containers
- **docker/nginx/default.conf** - Nginx configuration
- **docker/supervisor/supervisord.conf** - Process management

## Important Notes

- The application runs on port 8000 (mapped from container port 80)
- MySQL runs on port 3306 with database name 'laravel'
- Authentication is required for creating, editing, and deleting posts
- Users can only edit/delete their own posts (enforced by PostPolicy)
- Tailwind CSS is loaded via CDN for styling

## Deployment Considerations

For AWS EC2 deployment:
1. Ensure security groups allow ports 22 (SSH), 80 (HTTP), 443 (HTTPS), 8000 (app)
2. Set APP_ENV=production and APP_DEBUG=false in production
3. Use strong database passwords
4. Consider using RDS for production database
5. Set up SSL certificates for HTTPS