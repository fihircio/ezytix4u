# Payment Gateway Setup Guide

## Overview
This guide provides detailed instructions for setting up and configuring payment gateways in the EzyTix4U platform. The system supports multiple payment gateways to cater to different regions and payment preferences.

## Supported Payment Gateways

### 1. PayPal Express
**Best for**: International payments, global reach
**Supported Currencies**: USD, EUR, GBP, and many others
**Processing Time**: Instant

### 2. Billplz
**Best for**: Malaysian market
**Supported Currencies**: MYR
**Processing Time**: Instant

### 3. ToyyibPay
**Best for**: Malaysian market
**Supported Currencies**: MYR
**Processing Time**: Instant

### 4. USAePay
**Best for**: Credit card processing
**Supported Currencies**: USD
**Processing Time**: Instant

## Configuration Setup

### Environment Variables

Add these variables to your `.env` file:

```env
# PayPal Configuration
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_SECRET=your_paypal_secret
PAYPAL_MODE=sandbox  # or live

# Billplz Configuration
BILLPLZ_SECRET_KEY=your_billplz_secret_key
BILLPLZ_XSIGNATURE=your_billplz_xsignature
BILLPLZ_APP_ID=your_billplz_collection_id
BILLPLZ_REDIRECT_URI=https://yourdomain.com/bookings/billplz/callback

# ToyyibPay Configuration
TOYYIBPAY_SECRET_KEY=your_toyyibpay_secret_key
TOYYIBPAY_REDIRECT_URI=https://yourdomain.com/bookings/toyyibpay/callback

# USAePay Configuration
USAEPAY_SOURCE_KEY=your_usaepay_source_key
USAEPAY_PIN=your_usaepay_pin
```

### Admin Panel Configuration

1. **Access Admin Panel**
   - Login to your admin panel
   - Navigate to Settings > Apps

2. **Configure Payment Gateways**
   - Enter API keys and credentials
   - Set callback URLs
   - Configure webhook endpoints

## Gateway-Specific Setup

### PayPal Express Setup

#### 1. Create PayPal Developer Account
1. Visit [PayPal Developer Portal](https://developer.paypal.com/)
2. Create a developer account
3. Create a new app to get Client ID and Secret

#### 2. Configure PayPal Settings
```php
// In your admin panel settings
PayPal Client ID: [your_client_id]
PayPal Secret: [your_secret]
PayPal Mode: sandbox (for testing) or live (for production)
```

#### 3. Test PayPal Integration
```bash
# Test with PayPal sandbox
curl -X POST https://api.sandbox.paypal.com/v1/oauth2/token \
  -H "Authorization: Basic [base64_encoded_credentials]" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "grant_type=client_credentials"
```

### Billplz Setup

#### 1. Create Billplz Account
1. Visit [Billplz](https://www.billplz.com/)
2. Sign up for a merchant account
3. Get your API credentials

#### 2. Configure Billplz Settings
```php
// In your admin panel settings
Billplz Secret Key: [your_secret_key]
Billplz X-Signature: [your_x_signature]
Billplz Collection ID: [your_collection_id]
Billplz Redirect URI: https://yourdomain.com/bookings/billplz/callback
```

#### 3. Test Billplz Integration
```bash
# Test API connection
curl https://www.billplz.com/api/v4/collections \
  -u your_secret_key:
```

#### 4. Mobile Number Formatting
Billplz requires Malaysian phone numbers in specific format:
- Input: `0123456789`
- Output: `+60123456789`

The system automatically formats phone numbers for Billplz.

### ToyyibPay Setup

#### 1. Create ToyyibPay Account
1. Visit [ToyyibPay](https://toyyibpay.com/)
2. Register as a merchant
3. Get your API credentials

#### 2. Configure ToyyibPay Settings
```php
// In your admin panel settings
ToyyibPay Secret Key: [your_secret_key]
ToyyibPay Redirect URI: https://yourdomain.com/bookings/toyyibpay/callback
```

### USAePay Setup

#### 1. Create USAePay Account
1. Visit [USAePay](https://usaepay.com/)
2. Sign up for merchant services
3. Get your Source Key and PIN

#### 2. Configure USAePay Settings
```php
// In your admin panel settings
USAePay Source Key: [your_source_key]
USAePay PIN: [your_pin]
```

## Webhook Configuration

### Billplz Webhooks
```php
// Webhook URL: https://yourdomain.com/bookings/billplz/callback
// Events to listen for:
// - bill.created
// - bill.paid
// - bill.failed
```

### ToyyibPay Webhooks
```php
// Webhook URL: https://yourdomain.com/bookings/toyyibpay/callback
// Events to listen for:
// - payment.success
// - payment.failed
```

## Testing Payment Gateways

### Sandbox Testing
1. **PayPal Sandbox**
   - Use sandbox credentials
   - Test with sandbox PayPal accounts
   - No real money involved

2. **Billplz Testing**
   - Use test collection ID
   - Test with test phone numbers
   - Verify webhook responses

3. **ToyyibPay Testing**
   - Use test environment
   - Test with test transactions
   - Verify callback responses

### Production Testing
1. **Small Amount Testing**
   - Test with minimal amounts (RM1, $1)
   - Verify complete payment flow
   - Check webhook responses

2. **Refund Testing**
   - Test refund functionality
   - Verify refund webhooks
   - Check accounting accuracy

## Troubleshooting

### Common Issues

#### 1. Billplz Mobile Number Error
**Error**: `"Mobile is invalid"`
**Solution**: Ensure phone numbers are in Malaysian format (+60123456789)

#### 2. PayPal Timeout
**Error**: 408 timeout
**Solution**: 
- Check network connectivity
- Verify API credentials
- Check firewall settings

#### 3. Webhook Not Receiving
**Issue**: Webhooks not being received
**Solution**:
- Verify webhook URL is accessible
- Check SSL certificate
- Verify webhook signature

#### 4. Payment Processing Errors
**Error**: Payment failed
**Solution**:
- Check API credentials
- Verify currency settings
- Check amount formatting

### Debug Tools

#### 1. Log Monitoring
```bash
# Monitor payment logs
tail -f storage/logs/laravel.log | grep -i payment
```

#### 2. API Testing
```bash
# Test Billplz API
curl -v https://www.billplz.com/api/v4/collections \
  -u your_secret_key:

# Test PayPal API
curl -v https://api.sandbox.paypal.com/v1/identity/oauth2/userinfo \
  -H "Authorization: Bearer your_access_token"
```

#### 3. Webhook Testing
```bash
# Test webhook endpoint
curl -X POST https://yourdomain.com/bookings/billplz/callback \
  -H "Content-Type: application/json" \
  -d '{"test": "data"}'
```

## Security Best Practices

### 1. API Key Security
- Store API keys in environment variables
- Never commit API keys to version control
- Rotate API keys regularly
- Use different keys for sandbox and production

### 2. Webhook Security
- Verify webhook signatures
- Use HTTPS for all webhook URLs
- Implement webhook retry logic
- Monitor webhook failures

### 3. Data Protection
- Encrypt sensitive payment data
- Implement PCI compliance measures
- Regular security audits
- Monitor for suspicious activity

## Performance Optimization

### 1. Caching
```php
// Cache payment gateway configurations
Cache::remember('payment_config', 3600, function () {
    return PaymentConfig::all();
});
```

### 2. Async Processing
```php
// Process webhooks asynchronously
Queue::push(new ProcessPaymentWebhook($data));
```

### 3. Error Handling
```php
// Implement retry logic for failed payments
if ($payment->failed()) {
    $payment->retry();
}
```

## Monitoring and Analytics

### 1. Payment Metrics
- Success rate by gateway
- Average transaction time
- Error rates and types
- Revenue by payment method

### 2. Alert System
- Failed payment notifications
- Webhook failure alerts
- API timeout warnings
- Security breach alerts

### 3. Reporting
- Daily payment summaries
- Monthly revenue reports
- Gateway performance comparison
- Refund and chargeback tracking

## Support and Resources

### Documentation Links
- [PayPal Developer Documentation](https://developer.paypal.com/)
- [Billplz API Documentation](https://www.billplz.com/api)
- [ToyyibPay Documentation](https://toyyibpay.com/docs)
- [USAePay Documentation](https://usaepay.com/developers/)

### Contact Information
- **PayPal Support**: [PayPal Developer Support](https://developer.paypal.com/support/)
- **Billplz Support**: support@billplz.com
- **ToyyibPay Support**: support@toyyibpay.com
- **USAePay Support**: support@usaepay.com

### Community Resources
- Laravel Payment Gateway Community
- Malaysian E-commerce Developers
- Payment Processing Forums 