#!/bin/bash

# Laravel Blog Production Deployment Script
# This script deploys the application on AWS EC2 with RDS

set -e

echo "🚀 Starting Laravel Blog Production Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if .env.production exists, if not create from example
if [ ! -f ".env.production" ]; then
    if [ -f ".env.production.example" ]; then
        echo -e "${YELLOW}📋 Creating .env.production from example...${NC}"
        cp .env.production.example .env.production
        echo -e "${RED}❌ Please edit .env.production with your actual RDS settings before continuing!${NC}"
        echo "Edit the file: nano .env.production"
        echo "Then run this script again."
        exit 1
    else
        echo -e "${RED}❌ .env.production.example file not found!${NC}"
        echo "Please ensure you have the production environment template."
        exit 1
    fi
fi

echo -e "${YELLOW}📋 Pre-deployment checklist:${NC}"
echo "1. ✅ RDS database created and accessible"
echo "2. ✅ Security groups configured (80, 443, 22)"
echo "3. ✅ .env.production configured with RDS settings"
echo "4. ✅ Domain name pointed to EC2 (if applicable)"

# Copy production environment file
echo -e "${YELLOW}🔧 Setting up production environment...${NC}"
cp .env.production .env

# Stop any existing containers
echo -e "${YELLOW}⏹️  Stopping existing containers...${NC}"
docker-compose -f docker-compose.prod.yaml down || true

# Build and start production containers
echo -e "${YELLOW}🏗️  Building production containers...${NC}"
docker-compose -f docker-compose.prod.yaml up -d --build

# Wait for container to be ready
echo -e "${YELLOW}⏳ Waiting for application to be ready...${NC}"
sleep 10

# Run Laravel setup commands
echo -e "${YELLOW}🔑 Generating application key...${NC}"
docker-compose -f docker-compose.prod.yaml exec -T app php artisan key:generate --force

echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
docker-compose -f docker-compose.prod.yaml exec -T app php artisan migrate --force

echo -e "${YELLOW}📦 Caching configuration...${NC}"
docker-compose -f docker-compose.prod.yaml exec -T app php artisan config:cache
docker-compose -f docker-compose.prod.yaml exec -T app php artisan route:cache
docker-compose -f docker-compose.prod.yaml exec -T app php artisan view:cache

echo -e "${YELLOW}🔧 Setting proper permissions...${NC}"
docker-compose -f docker-compose.prod.yaml exec -T app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker-compose -f docker-compose.prod.yaml exec -T app chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Check if application is running
echo -e "${YELLOW}🔍 Testing application...${NC}"
if curl -f -s http://localhost > /dev/null; then
    echo -e "${GREEN}✅ Deployment successful! Application is running.${NC}"
    echo -e "${GREEN}🌐 Your application is available at: http://$(curl -s http://checkip.amazonaws.com)${NC}"
else
    echo -e "${RED}❌ Deployment failed! Application is not responding.${NC}"
    echo "Check logs with: docker-compose -f docker-compose.prod.yaml logs app"
    exit 1
fi

echo -e "${GREEN}🎉 Production deployment completed successfully!${NC}"
echo ""
echo -e "${YELLOW}📚 Useful commands:${NC}"
echo "View logs: docker-compose -f docker-compose.prod.yaml logs -f app"
echo "Run artisan commands: docker-compose -f docker-compose.prod.yaml exec app php artisan [command]"
echo "Update application: git pull && ./scripts/deploy-production.sh"