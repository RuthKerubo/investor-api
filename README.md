# Investor API

A Laravel backend service for importing investor data from CSV files and exposing it through RESTful APIs.

## Features

- CSV file import with validation
- RESTful API endpoints for investor data
- Aggregate endpoints (average age, average investment, total investments)
- Paginated investor listing
- Unit and Feature tests

## Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+

## Installation

1. Clone the repository:
```bash
git clone git@github.com:RuthKerubo/investor-api.git
cd investor-api
```

2. Install dependencies:
```bash
composer install
```

3. Setup environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Create the MySQL database:
```bash
mysql -u root -p
```
```sql
CREATE DATABASE investor_api;
CREATE USER 'investor_user'@'localhost' IDENTIFIED BY 'investor_password';
GRANT ALL PRIVILEGES ON investor_api.* TO 'investor_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

5. Run migrations:
```bash
php artisan migrate
```

6. Start the server:
```bash
php artisan serve
```

The API is now available at `http://localhost:8000/api`

## Testing the API

### Import the sample CSV:
```bash
curl -X POST http://localhost:8000/api/import -F "file=@investors_with_dates.csv"
```

### Get Average Age:
```bash
curl http://localhost:8000/api/investors/average-age
```

### Get Average Investment:
```bash
curl http://localhost:8000/api/investors/average-investment
```

### Get Total Investments:
```bash
curl http://localhost:8000/api/investors/total-investments
```

### List All Investors:
```bash
curl http://localhost:8000/api/investors
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/health | Health check |
| POST | /api/import | Import CSV file |
| GET | /api/investors | List all investors (paginated) |
| GET | /api/investors/average-age | Get average age |
| GET | /api/investors/average-investment | Get average investment |
| GET | /api/investors/total-investments | Get total investments |

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
```

## Design Decisions

- **Service-Oriented Architecture**: Business logic is separated into service classes for better testability and maintainability.
- **Database Design**: Investors and investments are stored in separate tables with a foreign key relationship, allowing one investor to have multiple investments on different dates.
- **Scalability**: The import service processes records efficiently, and the list endpoint uses pagination to handle large datasets (10k+ records).

## Assumptions

- CSV date format is DD-MM-YYYY
- One investment amount per date per investor
- Investor ID is unique and provided in the CSV