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

## 🔄 Contract Workflow System

The system provides a comprehensive contract lifecycle management system with professional approval workflows.

### Workflow Statuses

- **Draft** → **Pending Review** → **Under Review** → **Pending Approval** → **Approved** → **Active**
- **Active** → **Completed** / **Suspended** / **Expired** / **Terminated**
- **Expired** → **Renewed** / **Terminated**

### Approval Workflow

```bash
# Request approval for a contract
POST /api/contracts/{id}/request-approval
{
  "approvers": [
    {
      "user_id": 2,
      "level": 1,
      "required": true,
      "due_date": "2024-02-15"
    },
    {
      "user_id": 3,
      "level": 2,
      "required": true,
      "due_date": "2024-02-20"
    }
  ]
}

# Approve a contract
POST /api/contracts/{id}/approve
{
  "comments": "Contract approved after legal review"
}
```

### Contract Signing

```bash
# Sign a contract
POST /api/contracts/{id}/sign
{
  "signature_type": "digital"
}
```

### Contract Renewal

```bash
# Renew an expired contract
POST /api/contracts/{id}/renew
{
  "title": "Service Agreement 2024-2025",
  "expires_at": "2025-12-31",
  "variables": {
    "contract_amount": "7500.00",
    "start_date": "2024-01-01"
  },
  "auto_approve": true
}
```

## 🎯 Advanced Variable System

The system provides a sophisticated variable replacement system with type validation and constraints.

### Variable Types

- **Text**: General text with length constraints
- **Number**: Numeric values with range validation
- **Date**: Date values with format validation
- **Email**: Email address validation
- **Phone**: Phone number format validation
- **Currency**: Monetary amounts with precision
- **Percentage**: Percentage values (0-100)

### Variable Usage

```php
// Template content with variables
"This agreement is made between [company_name] and [client_name] on [start_date].
The total contract value is [contract_amount] and the project will be completed by [end_date]."

// Contract variables
[
  {
    "name": "company_name",
    "type": "text",
    "value": "Acme Corporation"
  },
  {
    "name": "contract_amount",
    "type": "currency",
    "value": "50000.00"
  },
  {
    "name": "start_date",
    "type": "date",
    "value": "2024-01-15"
  }
]
```

### Variable Validation

```bash
# Create contract with variables
POST /api/contracts
{
  "title": "Service Agreement",
  "template_id": 1,
  "variables": [
    {
      "name": "client_name",
      "type": "text",
      "value": "John Doe"
    },
    {
      "name": "contract_amount",
      "type": "currency",
      "value": "5000.00"
    }
  ]
}
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
- `GET /api/templates/search` - Search templates with filters
- `POST /api/templates/{id}/rate` - Rate template
- `GET /api/templates/suggestions` - Get personalized template suggestions
- `GET /api/templates/statistics` - Get template usage statistics

#### Performance & Monitoring
- `GET /api/performance/cache-stats` - Get cache performance statistics
- `GET /api/performance/memory-usage` - Get memory usage information
- `POST /api/performance/warm-cache` - Warm up application caches
- `GET /api/performance/query-optimization` - Get database optimization suggestions

## 🗄️ Database Schema

### Core Tables

- **users** - User accounts and profiles
- **contract_templates** - Contract template definitions with categories and ratings
- **contracts** - Generated contracts with workflow status and approval tracking
- **template_variables** - Template variable definitions with type validation
- **contract_variables** - Contract variable values with constraints
- **contract_parties** - Contract participants and signatories
- **contract_documents** - Generated documents (PDFs, etc.)

### Workflow Tables

- **contract_statuses** - Contract status history and transitions
- **contract_approvals** - Multi-level approval workflow management
- **contract_signatures** - Digital and electronic signature tracking
- **template_categories** - Template organization and classification
- **template_ratings** - Template rating and review system

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

### Cache Configuration

```php
// config/cache.php
'default' => env('CACHE_DRIVER', 'redis'),
'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
    ],
],
```

### Rate Limiting Configuration

```php
// config/rate_limiting.php
'api' => [
    'default' => 60, // requests per minute
    'auth' => 10,    // authentication endpoints
    'pdf_generation' => 20, // PDF generation endpoints
    'admin' => 1000, // admin users
    'premium' => 500, // premium users
],
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

Run specific test suites:

```bash
# Run contract tests only
php artisan test --filter=ContractTest

# Run template tests only
php artisan test --filter=TemplateTest

# Run workflow tests only
php artisan test --filter=WorkflowTest
```

### Test Data

The system includes comprehensive test factories for:
- User models with different roles
- Contract templates with variables
- Contract instances with workflow states
- Template categories and ratings
- Approval workflows and signatures

### Testing Best Practices

- All tests use database transactions for isolation
- Factories generate realistic test data
- Tests cover both success and failure scenarios
- API endpoints are tested with authentication
- Workflow transitions are validated

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

### Template Categories

- Employment & HR
- Business & Commercial
- Real Estate
- Service Agreements
- Sales & Marketing
- Technology & IT
- Financial & Investment
- Healthcare & Medical
- Education & Training
- Legal & Compliance
- Creative & Media
- Transportation & Logistics
- Manufacturing & Supply
- Consulting & Advisory
- General & Miscellaneous

## 🔒 Security Features

- **CSRF Protection**: Enabled for web routes
- **Rate Limiting**: Role-based API rate limiting (Admin: 1000/min, Premium: 500/min, Regular: 60/min)
- **Input Validation**: Comprehensive request validation with type checking
- **SQL Injection Protection**: Eloquent ORM with parameter binding
- **XSS Protection**: Output escaping and validation
- **Role-Based Access Control**: Fine-grained permissions with audit trails
- **Request Logging**: Comprehensive API request and response logging
- **Audit Trails**: Complete tracking of all contract changes and approvals
- **IP Tracking**: Request origin tracking for security monitoring
- **Error Handling**: Secure error responses without information leakage

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

- **Database Indexing**: Optimized queries with proper indexes and query optimization
- **Eager Loading**: Prevents N+1 query problems with intelligent relationship loading
- **Redis Caching**: Multi-level caching for statistics, templates, and user dashboards
- **Query Optimization**: Performance monitoring and optimization suggestions
- **Memory Management**: Memory usage tracking and optimization
- **Cache Warming**: Proactive cache population for better performance
- **Rate Limiting**: Prevents API abuse with role-based limits
- **Pagination**: Efficient data retrieval for large datasets
- **Performance Monitoring**: Real-time performance metrics and optimization

## 🔍 Monitoring & Logging

- **Laravel Logging**: Comprehensive application logging with structured data
- **Activity Logging**: User action tracking with detailed audit trails
- **Error Tracking**: Exception handling and logging with stack traces
- **Performance Monitoring**: Query execution time tracking and optimization
- **Request Logging**: API request/response logging with performance metrics
- **Cache Monitoring**: Redis performance metrics and hit rate analysis
- **Memory Monitoring**: Real-time memory usage tracking
- **Workflow Tracking**: Complete contract lifecycle monitoring
- **Approval Monitoring**: Approval workflow status and timing tracking

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 🚀 Performance Optimization

The system includes comprehensive performance optimization features for enterprise-scale deployments.

### Caching Strategy

```php
// Redis caching configuration
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_DB', 0),
    ],
    'cache' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_CACHE_DB', 1),
    ],
],
```

### Cache Warming

```bash
# Warm up application caches
POST /api/performance/warm-cache

# Get cache performance statistics
GET /api/performance/cache-stats

# Get memory usage information
GET /api/performance/memory-usage
```

### Query Optimization

```bash
# Get database optimization suggestions
GET /api/performance/query-optimization

# Monitor slow queries
GET /api/performance/slow-queries
```

## 📊 Monitoring & Analytics

### Performance Metrics

- **Response Times**: API endpoint performance tracking
- **Cache Hit Rates**: Redis performance analysis
- **Memory Usage**: Real-time memory consumption monitoring
- **Database Performance**: Query execution time analysis
- **Workflow Metrics**: Contract processing time tracking

### Health Checks

```bash
# System health check
GET /api/health

# Detailed system status
GET /api/health/detailed

# Performance metrics
GET /api/health/performance
```

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For support and questions:

- Create an issue in the repository
- Check the documentation
- Review the API endpoints

## 🔄 Changelog

### Version 2.0.0 (Current)
- **Complete Contract Workflow System**
  - Multi-level approval workflows
  - Digital and electronic signature support
  - Contract status management with controlled transitions
  - Automatic expiration handling and renewal system
  - Complete audit trails and workflow history

- **Advanced Template Management**
  - Template categorization with 15 predefined categories
  - Rating and review system
  - Advanced search and filtering capabilities
  - Template cloning and versioning
  - Personalized template suggestions

- **Professional PDF Generation**
  - Customizable PDF templates with professional styling
  - Dynamic variable replacement with type validation
  - Signature sections and metadata support
  - Multiple output formats and customization options

- **Enterprise Security Features**
  - Role-based API rate limiting
  - Comprehensive request logging and audit trails
  - Advanced error handling and security monitoring
  - IP tracking and security analytics

- **Performance Optimization**
  - Redis-based caching system
  - Query optimization and performance monitoring
  - Memory usage tracking and optimization
  - Cache warming and performance analytics

### Version 1.0.0
- Initial release
- User authentication system
- Contract management
- Template system
- PDF generation
- Role-based permissions
- Comprehensive API endpoints
