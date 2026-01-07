#!/bin/bash

echo "Starting Investor API..."

# Copy .env if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
    echo "Created .env file"
fi

# Build and start containers
docker-compose up -d --build

echo "Waiting for database to be ready..."
sleep 10

# Generate app key if not set
docker-compose exec app php artisan key:generate --no-interaction

# Run migrations
docker-compose exec app php artisan migrate --force

echo ""
echo "Investor API is ready!"
echo ""
echo "API Base URL: http://localhost:8000/api"
echo ""
echo "Available endpoints:"
echo "  POST /api/import                       - Import CSV file"
echo "  GET  /api/investors                    - List all investors"
echo "  GET  /api/investors/average-age        - Get average age"
echo "  GET  /api/investors/average-investment - Get average investment"
echo "  GET  /api/investors/total-investments  - Get total investments"
echo "  GET  /api/health                       - Health check"
echo ""
echo "To import the sample CSV:"
echo "  curl -X POST http://localhost:8000/api/import -F \"file=@investors_with_dates.csv\""
echo ""