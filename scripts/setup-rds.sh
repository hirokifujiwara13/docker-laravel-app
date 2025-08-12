#!/bin/bash

# RDS Setup Helper Script
# This script helps configure RDS for Laravel Blog

set -e

echo "🗄️  Laravel Blog - RDS Setup Helper"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}📋 RDS Configuration Guide${NC}"
echo ""

echo -e "${YELLOW}1. Create RDS MySQL Instance:${NC}"
echo "   - Engine: MySQL 8.0"
echo "   - Instance Class: db.t3.micro (free tier) or larger"
echo "   - Storage: 20 GB (minimum)"
echo "   - Database Name: laravel"
echo "   - Master Username: laravel"
echo "   - Master Password: [secure password]"
echo ""

echo -e "${YELLOW}2. Security Group Configuration:${NC}"
echo "   - Create security group for RDS"
echo "   - Inbound Rules:"
echo "     - Type: MySQL/Aurora"
echo "     - Protocol: TCP"
echo "     - Port: 3306"
echo "     - Source: EC2 Security Group (or specific IP)"
echo ""

echo -e "${YELLOW}3. Required Information:${NC}"
echo "   After RDS creation, you'll need:"
echo "   - RDS Endpoint (e.g., mydb.123456789.us-east-1.rds.amazonaws.com)"
echo "   - Port: 3306"
echo "   - Database Name: laravel"
echo "   - Username: laravel"
echo "   - Password: [your secure password]"
echo ""

echo -e "${YELLOW}4. Update .env.production:${NC}"
echo "   DB_HOST=your-rds-endpoint.region.rds.amazonaws.com"
echo "   DB_PORT=3306"
echo "   DB_DATABASE=laravel"
echo "   DB_USERNAME=laravel"
echo "   DB_PASSWORD=your-secure-password"
echo ""

echo -e "${YELLOW}5. Test Connection (from EC2):${NC}"
echo "   mysql -h your-rds-endpoint.region.rds.amazonaws.com -u laravel -p"
echo ""

echo -e "${GREEN}✅ Follow this guide to set up your RDS instance!${NC}"
echo -e "${BLUE}💡 Tip: Keep your RDS endpoint and credentials secure!${NC}"