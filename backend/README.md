# Contract Generator Pro - Backend

A powerful Laravel 10 backend API for the Contract Generator Pro application, providing comprehensive contract management, template handling, and user authentication.

## 🚀 Features

- **User Authentication & Authorization**: Laravel Sanctum-based API authentication with role-based permissions
- **Contract Management**: Full CRUD operations for contracts with variable substitution
- **Template System**: Flexible contract templates with customizable variables and categories
- **PDF Generation**: Professional PDF generation using DomPDF with customizable templates
- **Contract Workflow System**: Complete lifecycle management with approvals, signing, and renewal
- **Advanced Variable System**: Type-safe variable replacement with validation and constraints
- **Template Management**: Categories, ratings, search, filtering, and cloning capabilities
- **Role-Based Access Control**: Comprehensive permission system using Spatie Laravel Permission
- **API-First Design**: RESTful API endpoints for frontend integration
- **Advanced Security**: Rate limiting, request logging, and comprehensive audit trails
- **Performance Optimization**: Redis caching, query optimization, and performance monitoring
- **Professional Infrastructure**: Enterprise-grade error handling and response formatting
- **Database Seeding**: Sample data for development and testing

## 🛠️ Technology Stack

- **Framework**: Laravel 10
- **PHP Version**: 8.2+
- **Database**: MySQL 8.0 / PostgreSQL 15
- **Authentication**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission
- **PDF Generation**: DomPDF
- **Caching**: Redis
- **Testing**: PHPUnit
- **Code Quality**: Laravel Pint
- **Performance**: Query optimization, caching strategies
- **Security**: Rate limiting, audit trails, request logging

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- MySQL 8.0+ or PostgreSQL 15+
- Redis (recommended, for caching and performance)
- Node.js 16+ (for frontend assets compilation)
- 2GB+ RAM (for optimal performance)
- 10GB+ disk space

## 🚀 Quick Start

### 1. Clone and Setup

```bash
cd backend
composer install
cp .env.example .env
```

### 2. Environment Configuration

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=contract_generator_pro
DB_USERNAME=root
DB_PASSWORD=

# Redis Configuration (recommended)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache Configuration
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Mail Configuration (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@contractgeneratorpro.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Seed Database

```bash
php artisan db:seed
```

### 6. Start Development Server

```bash
php artisan serve
```

### 7. Start Redis (for caching)

```bash
# On macOS with Homebrew
brew services start redis

# On Ubuntu/Debian
sudo systemctl start redis-server

# On Windows with WSL
sudo service redis-server start
```

The API will be available at `http://localhost:8000/api`

## 🔐 Authentication

The backend uses Laravel Sanctum for API authentication. All protected endpoints require a valid Bearer token.

### Login Flow

1. **Register**: `POST /api/register`
2. **Login**: `POST /api/login`
3. **Use Token**: Include `Authorization: Bearer {token}` header

### Sample Login Request

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'
```

## 📚 API Endpoints

### Public Endpoints

- `POST /api/register` - User registration
- `POST /api/login` - User authentication
- `GET /api/templates` - List public templates
- `GET /api/templates/{id}` - Get template details
- `GET /api/health` - Health check

### Protected Endpoints

#### User Management
- `GET /api/user` - Get current user
- `PUT /api/profile` - Update profile
- `PUT /api/change-password` - Change password
- `POST /api/logout` - Logout
- `POST /api/refresh` - Refresh token

#### Contracts
- `GET /api/contracts` - List user contracts
- `POST /api/contracts` - Create contract
- `GET /api/contracts/{id}` - Get contract
- `PUT /api/contracts/{id}` - Update contract
- `DELETE /api/contracts/{id}` - Delete contract
- `POST /api/contracts/{id}/generate-pdf` - Generate PDF
- `POST /api/contracts/{id}/sign` - Sign contract
- `GET /api/contracts/statistics` - Get statistics

#### Contract Workflow
- `POST /api/contracts/{id}/change-status` - Change contract status
- `POST /api/contracts/{id}/request-approval` - Request contract approval
- `POST /api/contracts/{id}/approve` - Approve contract
- `POST /api/contracts/{id}/reject` - Reject contract
- `POST /api/contracts/{id}/renew` - Renew expired contract
- `GET /api/contracts/{id}/workflow-history` - Get workflow history
- `GET /api/contracts/{id}/approvals` - Get approval status
- `GET /api/contracts/expiring-soon` - Get contracts expiring soon

#### Templates
- `POST /api/templates` - Create template
- `PUT /api/templates/{id}` - Update template
- `DELETE /api/templates/{id}` - Delete template
- `POST /api/templates/{id}/clone` - Clone template
- `GET /api/templates/categories` - Get categories
- `GET /api/templates/popular` - Get popular templates
- `GET /api/templates/highly-rated` - Get highly rated templates

## 🗄️ Database Schema

### Core Tables

- **users** - User accounts and profiles
- **contract_templates** - Contract template definitions
- **contracts** - Generated contracts
- **template_variables** - Template variable definitions
- **contract_variables** - Contract variable values
- **contract_parties** - Contract participants
- **contract_documents** - Generated documents (PDFs, etc.)

### Permission Tables

- **roles** - User roles (user, premium, admin)
- **permissions** - System permissions
- **model_has_roles** - Role assignments
- **model_has_permissions** - Permission assignments

## 🔧 Configuration

### Sanctum Configuration

```php
// config/sanctum.php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,localhost:3000')),
'guard' => ['web'],
'expiration' => null,
```

### Permission Configuration

```php
// config/permission.php
'cache_expiration_time' => 60 * 24, // 24 hours
'display_permission_in_exception' => false,
'display_role_in_exception' => false,
```

## 🧪 Testing

Run the test suite:

```bash
php artisan test
```

Run with coverage:

```bash
php artisan test --coverage
```

## 📊 Sample Data

The seeder creates:

- **Admin User**: `admin@contractgeneratorpro.com` / `password`
- **Premium User**: `premium@example.com` / `password`
- **Regular User**: `user@example.com` / `password`
- **Demo User**: `demo@example.com` / `password`

### Sample Templates

- Service Agreement
- Employment Contract
- Non-Disclosure Agreement
- Partnership Agreement
- Rental Agreement

## 🔒 Security Features

- **CSRF Protection**: Enabled for web routes
- **Rate Limiting**: API rate limiting (60 requests/minute)
- **Input Validation**: Comprehensive request validation
- **SQL Injection Protection**: Eloquent ORM with parameter binding
- **XSS Protection**: Output escaping and validation
- **Role-Based Access Control**: Fine-grained permissions

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate strong `APP_KEY`
- [ ] Configure database credentials
- [ ] Set up SSL certificates
- [ ] Configure caching (Redis recommended)
- [ ] Set up monitoring (Sentry, Laravel Telescope)
- [ ] Configure backup system

### Environment Variables

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
```

## 📈 Performance

- **Database Indexing**: Optimized queries with proper indexes
- **Eager Loading**: Prevents N+1 query problems
- **Caching**: Redis-based caching for frequently accessed data
- **Rate Limiting**: Prevents API abuse
- **Pagination**: Efficient data retrieval for large datasets

## 🔍 Monitoring & Logging

- **Laravel Logging**: Comprehensive application logging
- **Activity Logging**: User action tracking
- **Error Tracking**: Exception handling and logging
- **Performance Monitoring**: Query execution time tracking

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For support and questions:

- Create an issue in the repository
- Check the documentation
- Review the API endpoints

## 🔄 Changelog

### Version 1.0.0
- Initial release
- User authentication system
- Contract management
- Template system
- PDF generation
- Role-based permissions
- Comprehensive API endpoints
