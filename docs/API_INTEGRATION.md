# API Integration Guide

## Overview
This guide provides detailed information for integrating with the Event Portal API, including authentication, endpoints, and best practices.

## Quick Start

### Authentication
```php
// Example: Obtaining an API token
$response = Http::post('https://api.eventportal.com/oauth/token', [
    'grant_type' => 'client_credentials',
    'client_id' => 'your_client_id',
    'client_secret' => 'your_client_secret',
    'scope' => 'event-management'
]);

$token = $response->json()['access_token'];
```

### Base URL
```
https://api.eventportal.com/v1
```

## Core Endpoints

### Events

#### List Events
```php
// Example: Fetching events with pagination
$response = Http::withToken($token)
    ->get('https://api.eventportal.com/v1/events', [
        'page' => 1,
        'per_page' => 20,
        'category' => 'music'
    ]);

$events = $response->json()['data'];
```

#### Create Event
```php
// Example: Creating a new event
$response = Http::withToken($token)
    ->post('https://api.eventportal.com/v1/events', [
        'title' => 'Summer Music Festival',
        'description' => 'Annual summer music festival',
        'start_date' => '2024-07-01',
        'end_date' => '2024-07-03',
        'venue' => 'Central Park',
        'category_id' => 1,
        'ticket_price' => 99.99,
        'total_tickets' => 1000
    ]);

$event = $response->json()['data'];
```

### Tickets

#### Purchase Tickets
```php
// Example: Purchasing tickets
$response = Http::withToken($token)
    ->post('https://api.eventportal.com/v1/tickets', [
        'event_id' => 1,
        'quantity' => 2,
        'payment_method' => 'credit_card',
        'card_details' => [
            'number' => '4242424242424242',
            'exp_month' => 12,
            'exp_year' => 2024,
            'cvc' => '123'
        ]
    ]);

$ticket = $response->json()['data'];
```

## Webhooks

### Setting Up Webhooks
```php
// Example: Registering a webhook
$response = Http::withToken($token)
    ->post('https://api.eventportal.com/v1/webhooks', [
        'url' => 'https://your-domain.com/webhook',
        'events' => ['ticket.purchased', 'event.created'],
        'secret' => 'your_webhook_secret'
    ]);
```

### Webhook Events
1. `event.created`
2. `event.updated`
3. `event.cancelled`
4. `ticket.purchased`
5. `ticket.cancelled`
6. `payment.succeeded`
7. `payment.failed`

### Webhook Security
```php
// Example: Verifying webhook signature
public function handleWebhook(Request $request)
{
    $signature = $request->header('X-EventPortal-Signature');
    $payload = $request->getContent();
    
    $expectedSignature = hash_hmac('sha256', $payload, config('services.eventportal.webhook_secret'));
    
    if (!hash_equals($expectedSignature, $signature)) {
        return response()->json(['error' => 'Invalid signature'], 401);
    }
    
    // Process webhook
}
```

## Error Handling

### Error Responses
```json
{
    "error": {
        "code": "validation_error",
        "message": "The given data was invalid.",
        "errors": {
            "title": ["The title field is required."],
            "start_date": ["The start date must be a date after today."]
        }
    }
}
```

### Error Codes
- `400`: Bad Request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Validation Error
- `429`: Too Many Requests
- `500`: Server Error

## Rate Limiting

### Limits
- 60 requests per minute
- 1000 requests per hour
- 10000 requests per day

### Headers
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1619123456
```

## Best Practices

### Authentication
1. Store tokens securely
2. Implement token refresh
3. Use environment variables
4. Rotate credentials regularly

### Error Handling
1. Implement retry logic
2. Log errors properly
3. Handle timeouts
4. Validate responses

### Performance
1. Use connection pooling
2. Implement caching
3. Batch requests
4. Use compression

### Security
1. Use HTTPS
2. Validate webhooks
3. Sanitize input
4. Monitor usage

## SDK Examples

### PHP SDK
```php
use EventPortal\Client;

$client = new Client([
    'api_key' => 'your_api_key',
    'api_secret' => 'your_api_secret'
]);

// Create event
$event = $client->events()->create([
    'title' => 'Tech Conference',
    'description' => 'Annual tech conference',
    'start_date' => '2024-09-01'
]);

// Purchase tickets
$ticket = $client->tickets()->purchase([
    'event_id' => $event->id,
    'quantity' => 2
]);
```

### JavaScript SDK
```javascript
import { EventPortal } from '@eventportal/sdk';

const client = new EventPortal({
    apiKey: 'your_api_key',
    apiSecret: 'your_api_secret'
});

// Create event
const event = await client.events.create({
    title: 'Tech Conference',
    description: 'Annual tech conference',
    startDate: '2024-09-01'
});

// Purchase tickets
const ticket = await client.tickets.purchase({
    eventId: event.id,
    quantity: 2
});
```

## Testing

### Sandbox Environment
```
https://sandbox-api.eventportal.com/v1
```

### Test Credentials
```php
// Sandbox credentials
$sandboxClient = new Client([
    'api_key' => 'test_key',
    'api_secret' => 'test_secret',
    'environment' => 'sandbox'
]);
```

### Test Cards
- Success: 4242424242424242
- Decline: 4000000000000002
- 3D Secure: 4000000000003220

## Migration Guide

### From v1 to v2
1. Update authentication
2. Update endpoints
3. Handle new responses
4. Update error handling

### Breaking Changes
1. Authentication changes
2. Response format changes
3. Endpoint changes
4. Parameter changes

## Support

### Resources
- API Documentation
- SDK Documentation
- Code Examples
- FAQ

### Contact
- Technical Support
- Account Management
- Sales Inquiries
- Security Issues 