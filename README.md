# Online Marketplace - Docker Setup

This is a PHP-based online marketplace application that can be easily deployed using Docker.

## Features

- User registration and authentication (buyers and sellers)
- Product management for sellers
- Shopping cart functionality for buyers
- Product search and browsing

## Prerequisites

- Docker
- Docker Compose

## Quick Start

1. **Clone or navigate to the project directory**
   ```bash
   cd sharon-public-marketplace
   ```

2. **Build and run the application**
   ```bash
   docker-compose up --build
   ```

3. **Access the application**
   - Open your browser and go to: http://localhost:8080
   - The application will be running with Apache web server
   - MySQL database will be automatically set up with the required schema

## Default Test Accounts

The application comes with two pre-configured test accounts:

- **Seller Account:**
  - Username: `admin_seller`
  - Password: `password`

- **Buyer Account:**
  - Username: `test_buyer`
  - Password: `password`

## Database

- **MySQL 8.0** is used as the database
- Database is automatically initialized with the required tables
- Database data is persisted in a Docker volume
- Database is accessible on `localhost:3306` (root password: `rootpassword`)

## File Structure

```
├── Dockerfile              # PHP Apache container configuration
├── docker-compose.yml      # Multi-container setup with MySQL
├── init.sql                # Database schema and sample data
├── config.php              # Database configuration
├── register.php            # User registration
├── login.php               # User authentication
├── logout.php              # User logout
├── dashboard.php           # User dashboard
├── add_product.php         # Product management (sellers)
├── products.php            # Product listing and search
└── add_to_cart.php         # Shopping cart functionality
```

## Development

### Stopping the Application
```bash
docker-compose down
```

### Rebuilding After Code Changes
```bash
docker-compose down
docker-compose up --build
```

### Viewing Logs
```bash
docker-compose logs web
docker-compose logs db
```

### Database Access
You can connect to the MySQL database using any MySQL client:
- Host: localhost
- Port: 3306
- Username: root
- Password: rootpassword
- Database: online_store

## Environment Variables

The application supports the following environment variables (configured in docker-compose.yml):

- `DB_HOST`: Database host (default: db)
- `DB_NAME`: Database name (default: online_store)
- `DB_USER`: Database username (default: root)
- `DB_PASS`: Database password (default: rootpassword)

## Troubleshooting

1. **Port conflicts**: If port 8081 or 3306 are already in use, modify the ports in docker-compose.yml
2. **Database connection**: Wait a few seconds after starting for the database to fully initialize

## Security Notes

This is a development setup. For production use, consider:
- Using environment files for sensitive data
- Implementing proper input validation and sanitization
- Adding HTTPS support
- Using non-root database users
- Implementing proper error handling