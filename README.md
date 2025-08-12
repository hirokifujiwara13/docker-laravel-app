# Laravel Blog Application with Docker

A simple blog application built with Laravel 11 and PHP 8.4, containerized with Docker for easy deployment on AWS EC2.

## Features

- User authentication (register, login, logout)
- Blog post CRUD operations
- User dashboard for managing posts
- Responsive design with Tailwind CSS
- Docker containerization for easy deployment
- MySQL database

## Prerequisites

- Docker and Docker Compose installed
- Git installed
- AWS EC2 instance (for deployment)

## Local Development Setup

1. **Clone the repository**
   ```bash
   git clone <your-repo-url>
   cd docker-larave-app
   ```

2. **Copy environment file**
   ```bash
   cp .env.example .env
   ```

3. **Build and start Docker containers**
   ```bash
   docker-compose up -d --build
   ```

4. **Install dependencies**
   ```bash
   docker-compose exec app composer install
   ```

5. **Generate application key**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

6. **Run database migrations**
   ```bash
   docker-compose exec app php artisan migrate
   ```

7. **Access the application**
   - Open your browser and navigate to `http://localhost:8000`

## AWS EC2 Deployment with RDS

### Step 1: Set Up RDS Database

1. **Create RDS MySQL Instance**
   ```bash
   # Run the RDS setup helper
   ./scripts/setup-rds.sh
   ```
   
   Or manually create via AWS Console:
   - Engine: MySQL 8.0
   - Instance Class: db.t3.micro (free tier) or larger
   - Storage: 20 GB minimum
   - Database Name: `laravel`
   - Master Username: `laravel`
   - Master Password: [choose a secure password]

2. **Configure Security Groups**
   - Create security group for RDS
   - Allow MySQL (port 3306) from EC2 security group

3. **Note your RDS details**
   - RDS Endpoint (e.g., `mydb.123456789.us-east-1.rds.amazonaws.com`)
   - Username: `laravel`
   - Password: [your secure password]

### Step 2: Prepare EC2 Instance

1. **Launch an EC2 instance**
   - Choose Ubuntu Server 22.04 LTS or Amazon Linux 2023
   - Instance type: t2.micro (for testing) or larger for production
   - Configure security group:
     - SSH (port 22) from your IP
     - HTTP (port 80) from anywhere
     - HTTPS (port 443) from anywhere (if using SSL)
     - Custom TCP (port 8000) from anywhere (for testing)

2. **Connect to your EC2 instance**
   ```bash
   ssh -i your-key.pem ec2-user@your-ec2-public-ip
   ```

3. **Install Docker and Docker Compose**
   
   For Ubuntu:
   ```bash
   # Update packages
   sudo apt update
   sudo apt upgrade -y

   # Install Docker
   curl -fsSL https://get.docker.com -o get-docker.sh
   sudo sh get-docker.sh
   sudo usermod -aG docker $USER

   # Install Docker Compose
   sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
   sudo chmod +x /usr/local/bin/docker-compose

   # Logout and login again for group changes to take effect
   exit
   ```

   For Amazon Linux 2023:
   ```bash
   # Install Docker
   sudo yum update -y
   sudo yum install docker -y
   sudo service docker start
   sudo usermod -a -G docker ec2-user

   # Install Docker Compose
   sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
   sudo chmod +x /usr/local/bin/docker-compose

   # Logout and login again
   exit
   ```

### Step 3: Deploy Application

1. **Clone the repository on EC2**
   ```bash
   git clone <your-repo-url>
   cd docker-larave-app
   ```

2. **Configure production environment**
   ```bash
   cp .env.production .env
   nano .env
   ```
   
   Update the following in `.env`:
   - `APP_URL=http://your-ec2-public-ip` (or your domain)
   - `DB_HOST=your-rds-endpoint.region.rds.amazonaws.com`
   - `DB_PASSWORD=your-secure-rds-password`
   - Generate a secure `APP_KEY`

3. **Run automated deployment**
   ```bash
   chmod +x scripts/deploy-production.sh
   ./scripts/deploy-production.sh
   ```
   
   Or manually:
   ```bash
   # Build production containers (without MySQL)
   docker-compose -f docker-compose.prod.yaml up -d --build
   
   # Initialize application
   docker-compose -f docker-compose.prod.yaml exec app php artisan key:generate --force
   docker-compose -f docker-compose.prod.yaml exec app php artisan migrate --force
   docker-compose -f docker-compose.prod.yaml exec app php artisan config:cache
   docker-compose -f docker-compose.prod.yaml exec app php artisan route:cache
   docker-compose -f docker-compose.prod.yaml exec app php artisan view:cache
   ```

4. **Verify deployment**
   ```bash
   # Check application status
   curl http://localhost
   
   # View logs if needed
   docker-compose -f docker-compose.prod.yaml logs -f app
   ```

### Step 3: Configure for Production

1. **Use a domain name (optional)**
   - Point your domain to the EC2 public IP
   - Update `APP_URL` in `.env`

2. **Set up SSL with Let's Encrypt (optional)**
   ```bash
   # Install Certbot
   sudo apt install certbot python3-certbot-nginx -y

   # Obtain certificate
   sudo certbot --nginx -d yourdomain.com
   ```

3. **Set up a reverse proxy with Nginx (optional)**
   
   Create `/etc/nginx/sites-available/laravel`:
   ```nginx
   server {
       listen 80;
       server_name your-domain.com;

       location / {
           proxy_pass http://localhost:8000;
           proxy_set_header Host $host;
           proxy_set_header X-Real-IP $remote_addr;
           proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
           proxy_set_header X-Forwarded-Proto $scheme;
       }
   }
   ```

   Enable the site:
   ```bash
   sudo ln -s /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl restart nginx
   ```

### Step 4: Monitoring and Maintenance

1. **View logs**
   ```bash
   docker-compose logs -f app
   docker-compose logs -f mysql
   ```

2. **Backup database**
   ```bash
   docker-compose exec mysql mysqldump -u laravel -ppassword laravel > backup.sql
   ```

3. **Update application**
   ```bash
   git pull origin main
   docker-compose down
   docker-compose up -d --build
   docker-compose exec app php artisan migrate --force
   docker-compose exec app php artisan cache:clear
   ```

## Docker Commands Reference

### Local Development (with Docker MySQL + phpMyAdmin)
```bash
# Start all services (including MySQL and phpMyAdmin)
docker-compose up -d

# Stop all services
docker-compose down

# View logs
docker-compose logs -f

# Execute commands in app container
docker-compose exec app php artisan [command]

# Access MySQL directly
docker-compose exec mysql mysql -u laravel -ppassword laravel

# Access phpMyAdmin: http://localhost:8080
# MySQL via Sequel Ace: localhost:3307, user: laravel, password: password
```

### Production Deployment (with RDS)
```bash
# Deploy to production (no MySQL container)
docker-compose -f docker-compose.prod.yaml up -d --build

# Stop production
docker-compose -f docker-compose.prod.yaml down

# View production logs
docker-compose -f docker-compose.prod.yaml logs -f app

# Run artisan commands in production
docker-compose -f docker-compose.prod.yaml exec app php artisan [command]

# Automated deployment
./scripts/deploy-production.sh
```

## Environment Variables

Key environment variables in `.env`:

- `APP_NAME` - Application name
- `APP_ENV` - Environment (local/production)
- `APP_KEY` - Application encryption key
- `APP_DEBUG` - Debug mode (true/false)
- `APP_URL` - Application URL
- `DB_HOST` - Database host (mysql for Docker)
- `DB_DATABASE` - Database name
- `DB_USERNAME` - Database username
- `DB_PASSWORD` - Database password

## Security Considerations

1. **For production deployment:**
   - Set `APP_ENV=production` and `APP_DEBUG=false`
   - Use strong passwords for database
   - Keep `.env` file secure and never commit it
   - Regularly update dependencies
   - Use HTTPS in production
   - Configure firewall rules properly
   - Regular backups

2. **AWS Security Best Practices:**
   - Use IAM roles for EC2 instances
   - Enable CloudWatch monitoring
   - Use AWS Systems Manager for patch management
   - Configure security groups with minimal required access
   - Use AWS Secrets Manager for sensitive data

## Troubleshooting

1. **Permission issues:**
   ```bash
   docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
   docker-compose exec app chmod -R 775 storage bootstrap/cache
   ```

2. **Database connection issues:**
   - Ensure MySQL container is running: `docker-compose ps`
   - Check database credentials in `.env`
   - Wait for MySQL to be ready: `docker-compose logs mysql`

3. **Application not accessible:**
   - Check if containers are running: `docker-compose ps`
   - Verify security group rules in AWS EC2
   - Check application logs: `docker-compose logs app`

## Support

For issues or questions, please create an issue in the repository.

## License

This project is open-sourced software licensed under the MIT license.