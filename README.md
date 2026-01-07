# Investor API

A Laravel backend service for importing investor data from CSV files and exposing it through RESTful APIs.

## Features

- CSV file import with validation
- RESTful API endpoints for investor data
- Aggregate endpoints (average age, average investment, total investments)
- Paginated investor listing
- Dockerized for easy setup
- Unit and Feature tests

## Requirements

- Docker and Docker Compose

OR for local development:

- PHP 8.2+
- Composer
- MySQL 8.0+

## Quick Start (Docker)

1. Clone the repository:
```bash
git clone git@github.com:RuthKerubo/investor-api.git
cd investor-api
```

2. Run the start script:
```bash
chmod +x docker/start.sh
./docker/start.sh
```

3. The API will be available at `http://localhost:8000/api`

## Local Development Setup

1. Clone the repository:
```bash
git clone git@github.com:RuthKerubo/investor-api.git
cd investor-api
```

2. Install dependencies:
```bash
composer install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Update `.env` with your database credentials:
```
DB_HOST=127.0.0.1
DB_DATABASE=investor_api
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run migrations:
```bash
php artisan migrate
```

6. Start the server:
```bash
php artisan serve
```

## API Endpoints

### Health Check
```
GET /api/health
```

### Import CSV
```
POST /api/import
Content-Type: multipart/form-data
Body: file (CSV file)
```

Example:
```bash
curl -X POST http://localhost:8000/api/import -F "file=@investors_with_dates.csv"
```

### Get Average Age
```
GET /api/investors/average-age
```

Response:
```json
{
  "success": true,
  "data": {
    "average_age": 47.04,
    "total_investors": 200
  }
}
```

### Get Average Investment
```
GET /api/investors/average-investment
```

Response:
```json
{
  "success": true,
  "data": {
    "average_investment_amount": 517396.36,
    "total_investments": 200
  }
}
```

### Get Total Investments
```
GET /api/investors/total-investments
```

Response:
```json
{
  "success": true,
  "data": {
    "total_investments": 200,
    "total_amount": 103479271.28
  }
}
```

### List All Investors
```
GET /api/investors
GET /api/investors?per_page=50
```

Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "investor_id": 1001,
      "name": "Daniel Nelson",
      "age": 28,
      "total_investment_amount": "328085.43"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 100,
    "total": 200
  }
}
```

## Running Tests
```bash
php artisan test
```

## Project Structure
```
app/
├── Http/Controllers/Api/
│   ├── ImportController.php     # Handles CSV imports
│   └── InvestorController.php   # Handles investor endpoints
├── Models/
│   ├── Investor.php
│   └── Investment.php
└── Services/
    ├── CsvImportService.php     # CSV parsing and import logic
    └── InvestorService.php      # Business logic for aggregations

database/migrations/
├── create_investors_table.php
└── create_investments_table.php

tests/
├── Feature/InvestorApiTest.php
└── Unit/InvestorServiceTest.php
```

## Design Decisions

- **Service-Oriented Architecture**: Business logic is separated into service classes for better testability and maintainability.
- **Database Design**: Investors and investments are stored in separate tables with a foreign key relationship, allowing one investor to have multiple investments on different dates.
- **Scalability**: The import service processes records efficiently, and the list endpoint uses pagination to handle large datasets (10k+ records).
- **Validation**: CSV imports include validation for required fields, data types, and date formats.

## Assumptions

- CSV date format is DD-MM-YYYY
- One investment amount per date per investor
- Investor ID is unique and provided in the CSV