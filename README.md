# EzyTix4U - Event Ticketing Platform

## Project Overview
EzyTix4U is a comprehensive event ticketing platform built with Laravel and Eventmie Pro, designed to handle event creation, management, and ticketing with multiple payment gateway integrations. The system provides a complete solution for event organizers and attendees across Malaysia and beyond.

## 🌟 Key Features

### Event Management
- **Event Creation & Management**: Complete event lifecycle management
- **Multi-Category Support**: Tech & Finance, Business & Seminars, Event & Concert
- **Venue Management**: Support for multiple cities (KL, Penang, Ipoh, Johor Bahru)
- **Seat Management**: Advanced seating chart with seat selection
- **Repetitive Events**: Support for recurring events with flexible scheduling

### Ticketing System
- **Multiple Ticket Types**: Regular, VIP, Early Bird tickets
- **Dynamic Pricing**: Flexible pricing strategies
- **Quantity Management**: Real-time availability tracking
- **Customer Limits**: Per-customer ticket purchase limits
- **Promo Codes**: Discount and promotional code system

### Payment Integration
- **Multi-Gateway Support**:
  - PayPal (International)
  - Billplz (Malaysia)
  - ToyyibPay (Malaysia)
  - USAePay (Credit Card Processing)
- **Secure Transactions**: PCI-compliant payment processing
- **Offline Payments**: Support for manual payment methods
- **Real-time Processing**: Instant payment verification

### User Management
- **Multi-Role System**: Admin, Organizer, Customer roles
- **Social Login**: Facebook and Google OAuth integration
- **Profile Management**: Complete user profile system
- **Email Verification**: Secure account verification

### Regional Support
- **Multi-Language**: Support for multiple languages
- **Timezone Management**: Flexible timezone configuration
- **Currency Support**: Multi-currency support (MYR, USD, etc.)
- **Regional Settings**: Country-specific configurations

## 🛠 Technical Stack

### Backend
- **Laravel 10+**: PHP framework
- **MySQL 8.0+**: Database
- **Redis**: Caching and session management
- **Queue System**: Background job processing

### Frontend
- **Vue.js**: Progressive JavaScript framework
- **Bootstrap**: UI framework
- **Laravel Mix**: Build tool (assets compiled in `eventmie-pro`)

### Payment Gateways
- **PayPal Express**: International payments
- **Billplz**: Malaysian payment gateway
- **ToyyibPay**: Malaysian payment gateway
- **USAePay**: Credit card processing

### Development Tools
- **Laravel Debugbar**: Development debugging
- **Laravel Telescope**: Application monitoring
- **PHPUnit**: Testing framework
- **Composer**: Dependency management

## 🚀 Quick Start

### Prerequisites
- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL 8.0+
- Redis (optional but recommended)

### Installation

1. **Clone the repository**
```bash
git clone [repository-url]
cd ezytix4u
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
```bash
# Update .env with your database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ezytix4u
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Run migrations and seeders**
```bash
php artisan migrate --seed
```

6. **Compile assets**
```bash
# Root-level assets (if any)
npm run dev

# Eventmie Pro assets
cd eventmie-pro
npm install
npm run dev
```

7. **Start development server**
```bash
php artisan serve
```

## 🔧 Configuration

### Payment Gateway Setup

#### PayPal Configuration
```env
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_SECRET=your_paypal_secret
PAYPAL_MODE=sandbox # or live
```

#### Billplz Configuration
```env
BILLPLZ_SECRET_KEY=your_billplz_secret_key
BILLPLZ_XSIGNATURE=your_billplz_xsignature
BILLPLZ_APP_ID=your_billplz_collection_id
BILLPLZ_REDIRECT_URI=https://yourdomain.com/bookings/billplz/callback
```

#### ToyyibPay Configuration
```env
TOYYIBPAY_SECRET_KEY=your_toyyibpay_secret_key
TOYYIBPAY_REDIRECT_URI=https://yourdomain.com/bookings/toyyibpay/callback
```

### Email Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="EzyTix4U"
```

## 📚 Documentation

### Core Documentation
- [API Documentation](docs/API.md) - Complete API reference
- [Database Schema](docs/DATABASE.md) - Database structure and relationships
- [Architecture Overview](docs/ARCHITECTURE.md) - System architecture and components
- [Development Guide](docs/DEVELOPMENT.md) - Development workflow and best practices
- [Project Structure](docs/PROJECT_STRUCTURE.md) - Where everything lives and how it connects

### Integration Guides
- [API Integration Guide](docs/API_INTEGRATION.md) - Third-party integration instructions
- [Payment Gateway Setup](docs/PAYMENT_SETUP.md) - Payment gateway configuration
- [Deployment Guide](docs/DEPLOYMENT.md) - Production deployment instructions

### Operational Documentation
- [User Guide](docs/USER_GUIDE.md) - End-user documentation
- [Monitoring Guide](docs/MONITORING.md) - System monitoring and alerting
- [Security Guide](docs/SECURITY.md) - Security best practices
- [Troubleshooting Guide](docs/TROUBLESHOOTING.md) - Common issues and solutions
- [Performance Guide](docs/PERFORMANCE.md) - Performance optimization

## 🔒 Security Features

- **JWT Authentication**: Secure token-based authentication
- **Role-Based Access Control**: Granular permission system
- **CSRF Protection**: Cross-site request forgery protection
- **XSS Prevention**: Input sanitization and output encoding
- **SQL Injection Prevention**: Parameterized queries
- **PCI Compliance**: Payment card industry compliance
- **SSL/TLS**: Secure communication protocols

## 📊 Monitoring & Analytics

- **Real-time Monitoring**: System health monitoring
- **Performance Metrics**: Response time and throughput tracking
- **Error Tracking**: Comprehensive error logging
- **Business Analytics**: Sales and attendance reporting
- **User Analytics**: User behavior and engagement metrics

## 🚀 Deployment

### Production Requirements
- **Web Server**: Nginx or Apache
- **PHP**: 8.1+ with required extensions
- **Database**: MySQL 8.0+ with proper indexing
- **Cache**: Redis for session and cache storage
- **SSL Certificate**: Valid SSL certificate for HTTPS
- **Backup System**: Automated database and file backups

### Environment Variables
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

## 🤝 Contributing

### Development Workflow
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Standards
- Follow PSR-12 coding standards
- Write comprehensive tests
- Update documentation for new features
- Follow Laravel best practices

## 📞 Support

### Getting Help
- **Documentation**: Check the comprehensive documentation in `/docs`
- **Issues**: Report bugs and feature requests via GitHub issues
- **Discussions**: Use GitHub discussions for questions and ideas

### Contact Information
- **Website**: [https://ezytix4u.com](https://ezytix4u.com)
- **Email**: support@ezytix4u.com
- **Technical Support**: Available for enterprise customers

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **Laravel Team**: For the amazing PHP framework
- **Eventmie Pro**: For the event management foundation
- **Payment Gateway Partners**: For seamless payment integration
- **Open Source Community**: For the tools and libraries that make this possible

---

**EzyTix4U** - Making event ticketing simple, secure, and scalable.
