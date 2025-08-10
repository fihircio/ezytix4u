# System Architecture

## Overview
The Event Portal is built on Laravel with Eventmie Pro integration, providing a comprehensive event management solution. The system follows a modular architecture with clear separation of concerns.

## Diagram

```mermaid
graph TD;
  A[Browser Request] --> B[routes/web.php<br/>Eventmie::routes()];
  B --> C[eventmie-pro/routes/eventmie.php];
  C --> D[Controllers<br/>(frontend/admin overrides)];
  D --> E[Blade Views<br/>(eventmie::...)];
  E --> F[Layout mounts #eventmie_app];
  F --> G[Page JS bundle<br/>(publishable/assets/js/*.js)];
  G --> H[Vue Components];

  %% Assets pipeline
  E --> I[include_css/include_js];
  I --> J[eventmie_asset() helper];
  J --> K[Route frontend-assets<br/>EventmieController@assets];
  K --> L[publishable/assets<br/>css/js/webfonts];

  %% Admin (Voyager)
  C --> M[/admin routes (Voyager)];
  M --> N[Voyager Controllers];
  N --> O[Voyager Views];
```

## System Components

### 1. Core Application (Laravel)
- **Models**: Business logic and data relationships
- **Controllers**: Request handling and response generation
- **Services**: Complex business operations
- **Middleware**: Request filtering and authentication
- **Events/Listeners**: Event-driven functionality
- **Jobs**: Background processing

### 2. Eventmie Pro Integration
- **Event Management**: Core event functionality
- **Ticket System**: Ticket creation and management
- **User Management**: User roles and permissions
- **Payment Processing**: PayPal Express, Billplz, ToyyibPay, USAePay integrations
- **Email Notifications**: Automated communication

### 3. Frontend (Vue.js)
- **Components**: Reusable UI elements
- **Views**: Page layouts and templates
- **Store**: State management
- **Router**: Navigation handling
- **API Integration**: Backend communication

## System Flow

### Event Creation Flow
1. Organizer logs in
2. Creates new event
3. Configures event details
4. Sets up tickets
5. Publishes event

### Ticket Purchase Flow
1. User browses events
2. Selects event
3. Chooses tickets
4. Proceeds to payment
5. Receives confirmation

### Payment Processing Flow
1. Payment initiated
2. Gateway integration
3. Transaction processing
4. Status update
5. Notification dispatch

## Integration Points

### External Services
1. **Payment Gateways**
   - Stripe
   - PayPal
   - Other payment providers

2. **Email Services**
   - SMTP servers
   - Email templates
   - Notification system

3. **Storage Services**
   - File storage
   - Image processing
   - CDN integration

### Internal Services
1. **Authentication**
   - User authentication
   - Role management
   - Permission control

2. **Caching**
   - Data caching
   - Query optimization
   - Performance tuning

3. **Queue System**
   - Background jobs
   - Email queuing
   - Task scheduling

## Package Integration Details (Eventmie Pro)

- Service Provider: `eventmie-pro/src/EventmieServiceProvider.php` registers third-party providers (Voyager, Socialite, Ziggy, Charts, DataTables, DomPDF, Honeypot), aliases middlewares, loads package views/translations/migrations, and merges config.
- Routes: `routes/web.php` calls `Eventmie::routes()` which includes `eventmie-pro/routes/eventmie.php` for all site and admin routes (welcome, events, bookings, profile, dashboard, voyager overrides).
- Views: Blade templates under `eventmie-pro/resources/views` are referenced via the `eventmie::` namespace. You can override by placing files under `resources/views/vendor/eventmie`.
- Assets: Built with Laravel Mix inside `eventmie-pro` and served via `eventmie_asset()` helper through the `frontend-assets` route from `EventmieController@assets`.
- Admin (Voyager): Admin routes are available under `/admin` by default and are extended/overridden by controllers in `eventmie-pro/src/Http/Controllers/Voyager` with views under `eventmie-pro/resources/views/vendor/voyager`.

## Request and Asset Flow

1. Request hits Laravel → `routes/web.php` → `Eventmie::routes()` → `eventmie-pro/routes/eventmie.php`.
2. Controller returns `eventmie::...` blade → layout `eventmie::layouts.app` mounts `#eventmie_app`.
3. Page-specific JS from `eventmie-pro/resources/js/...` compiled to `publishable/assets/js/*.js` is included via `eventmie_asset('js/xyz.js')` and initializes Vue on `#eventmie_app`.

## Security Architecture

### Authentication
- JWT token-based authentication
- Role-based access control
- Session management
- Password hashing

### Data Protection
- Input validation
- XSS prevention
- CSRF protection
- SQL injection prevention

### API Security
- Rate limiting
- Token validation
- Request signing
- CORS configuration

## Performance Architecture

### Caching Strategy
1. **Application Cache**
   - Route caching
   - Config caching
   - View caching

2. **Data Cache**
   - Query results
   - API responses
   - Static content

### Database Optimization
1. **Indexing**
   - Primary keys
   - Foreign keys
   - Composite indexes

2. **Query Optimization**
   - Eager loading
   - Query caching
   - Query optimization

### Asset Optimization
1. **Frontend Assets**
   - Code splitting
   - Lazy loading
   - Asset minification

2. **Image Optimization**
   - Compression
   - Responsive images
   - CDN delivery

## Deployment Architecture

### Server Requirements
- PHP 8.1+
- MySQL 8.0+
- Node.js 16+
- Composer
- NPM

### Environment Configuration
1. **Development**
   - Local environment
   - Debug mode
   - Development tools

2. **Staging**
   - Testing environment
   - Performance testing
   - User acceptance

3. **Production**
   - Live environment
   - Security measures
   - Monitoring tools

### Deployment Process
1. **Preparation**
   - Code review
   - Testing
   - Documentation

2. **Deployment**
   - Database migration
   - Asset compilation
   - Configuration update

3. **Verification**
   - Smoke testing
   - Performance check
   - Security scan

## Monitoring Architecture

### Application Monitoring
1. **Performance Metrics**
   - Response time
   - Error rates
   - Resource usage

2. **Business Metrics**
   - User activity
   - Transaction volume
   - Revenue tracking

### System Monitoring
1. **Server Health**
   - CPU usage
   - Memory usage
   - Disk space

2. **Network Health**
   - Bandwidth usage
   - Connection status
   - API availability

## Backup Architecture

### Data Backup
1. **Database Backup**
   - Daily backups
   - Point-in-time recovery
   - Backup verification

2. **File Backup**
   - User uploads
   - System files
   - Configuration files

### Recovery Procedures
1. **Data Recovery**
   - Database restoration
   - File restoration
   - Configuration recovery

2. **Disaster Recovery**
   - System restoration
   - Service recovery
   - Business continuity 