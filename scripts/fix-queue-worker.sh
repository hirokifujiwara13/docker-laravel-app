#!/bin/bash

# Fix Queue Worker in Production
echo "🔧 Fixing Queue Worker Issues..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}1. Checking supervisor status...${NC}"
docker-compose -f docker-compose.prod.yaml exec app supervisorctl status

echo -e "${YELLOW}2. Restarting queue workers...${NC}"
docker-compose -f docker-compose.prod.yaml exec app supervisorctl restart laravel-worker:*

echo -e "${YELLOW}3. Checking queue worker logs...${NC}"
echo "Recent worker logs:"
docker-compose -f docker-compose.prod.yaml exec app tail -20 /var/log/supervisor/laravel-worker.log

echo -e "${YELLOW}4. Testing manual queue processing...${NC}"
echo "Processing one job manually:"
docker-compose -f docker-compose.prod.yaml exec app php artisan queue:work --once --verbose

echo -e "${YELLOW}5. Checking jobs in database...${NC}"
docker-compose -f docker-compose.prod.yaml exec app php artisan queue:status

echo -e "${GREEN}✅ Queue worker diagnostics complete!${NC}"
echo ""
echo -e "${YELLOW}💡 If jobs still not processing:${NC}"
echo "1. Check RDS connection: docker-compose -f docker-compose.prod.yaml exec app php artisan tinker"
echo "2. Check logs: docker-compose -f docker-compose.prod.yaml exec app tail -f /var/log/supervisor/laravel-worker.log"
echo "3. Restart all: docker-compose -f docker-compose.prod.yaml restart"