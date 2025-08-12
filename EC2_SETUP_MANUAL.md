# Laravel Blog - EC2 Setup Manual

Complete step-by-step guide for deploying Laravel Blog on Amazon Linux 2023 with RDS.

## Prerequisites

- ✅ AWS EC2 instance (Amazon Linux 2023) launched
- ✅ SSH key pair configured
- ✅ Security groups configured (ports 22, 80, 443)
- ✅ RDS MySQL instance created (see RDS Setup section below)

---

## Step 1: RDS Database Setup

### 1.1 Create RDS Instance via AWS Console

1. Go to **RDS Dashboard** → **Create database**
2. **Engine**: MySQL 8.0
3. **Template**: Free tier (for testing) or Production
4. **DB Instance Identifier**: `laravel-blog-db`
5. **Master username**: `laravel`
6. **Master password**: `YourSecurePassword123!` (choose your own)
7. **Database name**: `laravel`
8. **Storage**: 20 GB (minimum)
9. **Public access**: No (recommended)
10. **VPC Security Group**: Create new or use existing
11. Click **Create database**

### 1.2 Configure RDS Security Group

1. Go to **EC2 Dashboard** → **Security Groups**
2. Find your RDS security group
3. **Edit inbound rules**:
   - **Type**: MySQL/Aurora
   - **Protocol**: TCP
   - **Port**: 3306
   - **Source**: Your EC2 security group ID (sg-xxxxxxxxx)
4. Save rules

### 1.3 Note RDS Details

After creation (takes 5-10 minutes), note:
- **Endpoint**: `laravel-blog-db.xxxxxxxxx.us-east-1.rds.amazonaws.com`
- **Port**: 3306
- **Username**: laravel
- **Password**: YourSecurePassword123!
- **Database**: laravel

---

## Step 2: Connect to EC2 Instance

```bash
# Replace with your details
ssh -i your-key-pair.pem ec2-user@your-ec2-public-ip
```

---

## Step 3: Install Docker and Dependencies

### 3.1 Update System
```bash
sudo yum update -y
```

### 3.2 Install Docker
```bash
# Install Docker
sudo yum install docker -y

# Start Docker service
sudo service docker start

# Add ec2-user to docker group
sudo usermod -a -G docker ec2-user

# Enable Docker to start on boot
sudo chkconfig docker on
```

### 3.3 Install Docker Compose
```bash
# Download Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose

# Make executable
sudo chmod +x /usr/local/bin/docker-compose

# Create symlink (optional, for easier access)
sudo ln -s /usr/local/bin/docker-compose /usr/local/bin/docker-compose
```

### 3.4 Install Git (if not already installed)
```bash
sudo yum install git -y
```

### 3.5 Logout and Login Again
```bash
# This is important for group permissions to take effect
exit

# SSH back in
ssh -i your-key-pair.pem ec2-user@your-ec2-public-ip
```

### 3.6 Verify Installation
```bash
# Check Docker
docker --version
docker-compose --version

# Test Docker (should work without sudo)
docker run hello-world
```

---

## Step 4: Clone and Setup Application

### 4.1 Create Application Directory
```bash
# Create directory
sudo mkdir -p /opt/laravel

# Change ownership to ec2-user
sudo chown ec2-user:ec2-user /opt/laravel

# Navigate to directory
cd /opt/laravel
```

### 4.2 Clone Repository
```bash
# Replace with your repository URL
git clone https://github.com/your-username/your-repo-name.git .

# Verify files
ls -la
```

### 4.3 Set File Permissions
```bash
# Make scripts executable
chmod +x scripts/*.sh

# Set proper permissions for Laravel
sudo chown -R ec2-user:ec2-user /opt/laravel
```

---

## Step 5: Configure Production Environment

### 5.1 Copy Production Environment File
```bash
cp .env.production .env
```

### 5.2 Edit Environment Configuration
```bash
nano .env
```

**Update these values in .env:**
```bash
# Application
APP_NAME="Laravel Blog"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://YOUR_EC2_PUBLIC_IP

# Database (Replace with your RDS details)
DB_HOST=laravel-blog-db.xxxxxxxxx.us-east-1.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=YourSecurePassword123!

# Mail (optional - configure if needed)
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Save and exit:** `Ctrl + X`, then `Y`, then `Enter`

---

## Step 6: Deploy Application

### 6.1 Run Automated Deployment
```bash
./scripts/deploy-production.sh
```

**What this script does:**
- Builds Docker containers (without MySQL)
- Generates application key
- Runs database migrations on RDS
- Caches Laravel configuration
- Sets proper file permissions
- Tests the application

### 6.2 Manual Deployment (if automatic fails)
```bash
# Build production containers
docker-compose -f docker-compose.prod.yaml up -d --build

# Wait for containers to start
sleep 15

# Generate application key
docker-compose -f docker-compose.prod.yaml exec -T app php artisan key:generate --force

# Run migrations (this connects to RDS)
docker-compose -f docker-compose.prod.yaml exec -T app php artisan migrate --force

# Cache configuration
docker-compose -f docker-compose.prod.yaml exec -T app php artisan config:cache
docker-compose -f docker-compose.prod.yaml exec -T app php artisan route:cache
docker-compose -f docker-compose.prod.yaml exec -T app php artisan view:cache

# Set permissions
docker-compose -f docker-compose.prod.yaml exec -T app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
```

---

## Step 7: Verify Deployment

### 7.1 Check Application Status
```bash
# Test local access
curl http://localhost

# Check container status
docker-compose -f docker-compose.prod.yaml ps

# View logs if needed
docker-compose -f docker-compose.prod.yaml logs app
```

### 7.2 Test from Browser
1. Get your EC2 public IP: `curl http://checkip.amazonaws.com`
2. Open browser: `http://YOUR_EC2_PUBLIC_IP`
3. You should see the Laravel Blog homepage

### 7.3 Test Database Connection
```bash
# Test RDS connection from EC2
mysql -h laravel-blog-db.xxxxxxxxx.us-east-1.rds.amazonaws.com -u laravel -p

# If successful, you'll see MySQL prompt
# Type 'exit' to quit
```

---

## Step 8: Test Application Features

1. **Homepage**: `http://YOUR_EC2_PUBLIC_IP`
2. **Register**: Create a new user account
3. **Login**: Test authentication
4. **Create Post**: Test blog functionality
5. **Queue/Scheduler Test**: `http://YOUR_EC2_PUBLIC_IP/test`

---

## Useful Commands

### Application Management
```bash
# View application logs
docker-compose -f docker-compose.prod.yaml logs -f app

# Restart application
docker-compose -f docker-compose.prod.yaml restart app

# Update application
cd /opt/laravel
git pull origin main
./scripts/deploy-production.sh

# Run artisan commands
docker-compose -f docker-compose.prod.yaml exec app php artisan [command]
```

### System Monitoring
```bash
# Check disk space
df -h

# Check memory usage
free -h

# Check running containers
docker ps

# Check system processes
top
```

---

## Troubleshooting

### If Application Won't Start
```bash
# Check container logs
docker-compose -f docker-compose.prod.yaml logs app

# Check if port 80 is in use
sudo netstat -tulpn | grep :80

# Restart containers
docker-compose -f docker-compose.prod.yaml down
docker-compose -f docker-compose.prod.yaml up -d --build
```

### If Database Connection Fails
```bash
# Test RDS connection
mysql -h YOUR_RDS_ENDPOINT -u laravel -p

# Check security groups
# Make sure EC2 security group can access RDS on port 3306

# Check .env file
cat .env | grep DB_
```

### If Website Shows 500 Error
```bash
# Check Laravel logs inside container
docker-compose -f docker-compose.prod.yaml exec app tail -f /var/www/storage/logs/laravel.log

# Clear all caches
docker-compose -f docker-compose.prod.yaml exec app php artisan cache:clear
docker-compose -f docker-compose.prod.yaml exec app php artisan config:clear
docker-compose -f docker-compose.prod.yaml exec app php artisan route:clear
```

---

## Security Considerations

1. **Change default passwords** in .env file
2. **Use HTTPS** in production (configure SSL certificate)
3. **Keep RDS in private subnet** (not publicly accessible)
4. **Regular updates**: `sudo yum update -y`
5. **Monitor logs** regularly
6. **Backup RDS** regularly via AWS snapshots

---

## Next Steps

1. **Domain Setup**: Point your domain to EC2 public IP
2. **SSL Certificate**: Use Let's Encrypt or AWS Certificate Manager
3. **Load Balancer**: For high availability (optional)
4. **Monitoring**: Set up CloudWatch for monitoring
5. **Backup Strategy**: Automated RDS snapshots

---

## Support

If you encounter issues:
1. Check the troubleshooting section above
2. Review application logs: `docker-compose -f docker-compose.prod.yaml logs app`
3. Check AWS RDS and EC2 console for any alerts
4. Verify security group configurations

**Deployment completed successfully!** 🎉