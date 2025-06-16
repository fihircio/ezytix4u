# API Documentation

## Authentication
All API endpoints require authentication using Bearer token.

```bash
Authorization: Bearer <your-token>
```

## Base URL
```
https://your-domain.com/api/v1
```

## Endpoints

### Events

#### List Events
```http
GET /events
```

Query Parameters:
- `page` (optional): Page number for pagination
- `per_page` (optional): Number of items per page
- `category` (optional): Filter by category
- `search` (optional): Search term

Response:
```json
{
    "data": [
        {
            "id": 1,
            "title": "Event Title",
            "description": "Event Description",
            "start_date": "2024-04-24",
            "end_date": "2024-04-25",
            "venue": "Event Venue",
            "category": "Category Name",
            "ticket_price": 100.00,
            "available_tickets": 50
        }
    ],
    "meta": {
        "current_page": 1,
        "total": 100,
        "per_page": 15
    }
}
```

#### Get Event Details
```http
GET /events/{id}
```

Response:
```json
{
    "data": {
        "id": 1,
        "title": "Event Title",
        "description": "Event Description",
        "start_date": "2024-04-24",
        "end_date": "2024-04-25",
        "venue": "Event Venue",
        "category": "Category Name",
        "ticket_price": 100.00,
        "available_tickets": 50,
        "organizer": {
            "id": 1,
            "name": "Organizer Name",
            "email": "organizer@example.com"
        }
    }
}
```

### Tickets

#### Purchase Ticket
```http
POST /tickets
```

Request Body:
```json
{
    "event_id": 1,
    "quantity": 2,
    "payment_method": "credit_card"
}
```

Response:
```json
{
    "data": {
        "ticket_id": "TICKET-123456",
        "event_id": 1,
        "quantity": 2,
        "total_amount": 200.00,
        "status": "pending",
        "payment_url": "https://payment-gateway.com/pay/123"
    }
}
```

## Error Responses

All endpoints may return the following error responses:

### 400 Bad Request
```json
{
    "error": "Validation Error",
    "message": "The given data was invalid.",
    "errors": {
        "field_name": ["Error message"]
    }
}
```

### 401 Unauthorized
```json
{
    "error": "Unauthorized",
    "message": "Invalid or expired token"
}
```

### 404 Not Found
```json
{
    "error": "Not Found",
    "message": "Resource not found"
}
```

### 500 Server Error
```json
{
    "error": "Server Error",
    "message": "Internal server error"
}
```

## Rate Limiting
API requests are limited to 60 requests per minute per IP address. The current rate limit status is included in the response headers:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1619123456
```

## Best Practices
1. Always handle rate limiting in your implementation
2. Implement proper error handling
3. Cache responses when appropriate
4. Use pagination for large data sets
5. Include proper authentication headers 