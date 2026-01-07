# API Documentation

## Authentication
All API endpoints require authentication using Bearer token (Laravel Sanctum).

```bash
Authorization: Bearer <your-token>
```

## Base URL
```
https://your-domain.com/api/v1
```

## Endpoints

### Core Laravel API

#### User Information
```http
GET /api/user
```
Returns authenticated user information.

### Event Management APIs

#### List Events
```http
GET /events/api/get_events
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

#### Get Event Categories
```http
GET /events/api/categories
```
Returns all available event categories.

#### Get Event Cities
```http
GET /events/api/cities
```
Returns cities where events are held.

#### Check Event Session
```http
POST /events/api/check/session
```
Checks availability of event sessions.

#### Search Events
```http
GET /events/search-events
```
Search events with various filters.

#### Get Event by Short URL
```http
GET /events/{event_short_url}
```
Access event details via short URL.

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

### Ticket Management APIs

#### Get Tickets
```http
POST /tickets/api
```
Retrieve tickets for a specific event.

#### Get Tax Information
```http
GET /tickets/api/taxes
```
Returns tax information for tickets.

#### Create Tickets
```http
POST /tickets/api/store
```
Create new tickets for an event.

#### Delete Tickets
```http
POST /tickets/api/delete
```
Remove existing tickets.

#### Purchase Ticket
```http
POST /bookings/api/book_tickets
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

### Booking APIs

#### Get Tickets for Booking
```http
POST /bookings/api/get_tickets
```
Get available tickets for booking.

#### Process Bookings
```http
POST /bookings/api/book_tickets
```
Process ticket bookings.

#### Payment Gateway Callbacks
```http
GET /bookings/paypal/callback
POST /bookings/paypal/callback
```
PayPal payment callback handler.

```http
GET /bookings/billplz/callback
POST /bookings/billplz/callback
```
Billplz payment callback handler.

```http
GET /bookings/toyyibpay/callback
POST /bookings/toyyibpay/callback
```
Toyyibpay payment callback handler.

```http
GET /bookings/chipin/callback
```
Chipin payment callback handler.

```http
POST /bookings/chipin/overview/callbacks
```
Chipin webhook notifications handler.

### Organizer Dashboard APIs

#### Get My Events
```http
GET /dashboard/myevents/api/get_myevents
```
Get events created by the authenticated organizer.

#### Get All My Events
```http
GET /dashboard/myevents/api/get_all_myevents
```
Get all events by organizer (including unpublished).

#### Create Event
```http
POST /dashboard/myevents/api/store
```
Create a new event.

#### Upload Event Media
```http
POST /dashboard/myevents/api/store_media
```
Upload media files for events.

#### Set Event Location
```http
POST /dashboard/myevents/api/store_location
```
Set event location details.

#### Set Event Timing
```http
POST /dashboard/myevents/api/store_timing
```
Set event date and time.

#### Add Event Tags
```http
POST /dashboard/myevents/api/store_event_tags
```
Add tags to events.

#### Set SEO Metadata
```http
POST /dashboard/myevents/api/store_seo
```
Set SEO metadata for events.

#### Get Countries
```http
GET /dashboard/myevents/api/countries
```
Get list of countries for event locations.

#### Get Specific Event
```http
POST /dashboard/myevents/api/get_myevent
```
Get details of a specific event by ID.

#### Publish/Unpublish Event
```http
POST /dashboard/myevents/api/publish_myevent
```
Publish or unpublish an event.

#### Get Event Organizers
```http
POST /dashboard/myevents/api/myevent_organizers
```
Get organizers for an event.

### Venue Management APIs

#### Search Venues
```http
POST /venues/api/search/venues
```
Search for available venues.

#### Request Venue Quote
```http
POST /venues/request_quote
```
Request a quote for venue booking.

#### Manage My Venues
```http
GET /dashboard/myvenues
POST /dashboard/myvenues
```
CRUD operations for venues managed by organizers.

#### Delete Venue Image
```http
POST /dashboard/myvenues/delete_venueimage/{venue}
```
Delete images associated with a venue.

### Booking Management APIs

#### Get My Bookings (Customer)
```http
GET /mybookings/api/get_mybookings
```
Get bookings for the authenticated customer.

#### Cancel Booking
```http
POST /mybookings/api/cancel
```
Cancel an existing booking.

#### Get Organizer Bookings
```http
GET /dashboard/mybookings/api/organiser_bookings
```
Get bookings for events managed by the organizer.

#### Edit Booking
```http
POST /dashboard/mybookings/api/organiser_bookings_edit
```
Update booking details.

#### Get Booking Customers
```http
POST /dashboard/mybookings/api/booking_customers
```
Get customer information for bookings.

### Additional Feature APIs

#### Tags Management
```http
POST /dashboard/mytags/api
GET /dashboard/mytags/api
```
Manage event tags.

```http
POST /dashboard/mytags/api/add
```
Add new tags.

```http
POST /dashboard/mytags/api/delete
```
Delete existing tags.

```http
POST /dashboard/mytags/api/selected/tags
```
Get tags selected for an event.

```http
POST /dashboard/mytags/api/search/tags
```
Search tags.

#### Event Schedules
```http
POST /schedules/api
```
Get event schedules.

```http
POST /schedules/api/event_schedule
```
Get schedule for a specific event.

#### Promo Codes
```http
GET /promocodes/get
```
Get available promo codes.

```http
GET /promocodes/get/ticket/{ticket_id}
```
Get promo codes for a specific ticket.

```http
POST /promocodes/apply
```
Apply a promo code to a booking.

#### Lucky Draw
```http
GET /dashboard/luckydraw
```
Get lucky draw information.

```http
GET /dashboard/luckydraw/participants
```
Get lucky draw participants.

#### Profile Management
```http
POST /profile/updateAuthUser
```
Update user profile information.

```http
POST /profile/updatePasswordUser
```
Update user password.

```http
POST /profile/updateBankUser
```
Update bank information.

```http
POST /profile/updateOrganiserUser
```
Update organizer-specific information.

#### Notifications
```http
GET /notifications/read/{n_type}
```
Mark notifications as read and redirect accordingly.

#### Seat Chart Management
```http
POST /seatschart/upload
```
Upload seat chart for events.

```http
POST /seatschart/disable_enable_seatchart
```
Enable or disable seat chart functionality.

#### Seat Management
```http
POST /seats/save
```
Save seat configuration.

```http
POST /seats/delete
```
Delete specific seats.

```http
POST /seats/disable
POST /seats/enable
```
Disable or enable individual seats.

```http
POST /seats/delete/all/seats
```
Delete all seats for an event.

#### Commission Management
```http
POST /commission/update
```
Update commission settings.

```http
POST /commission/settlement_update
```
Update settlement information.

#### QR Code Scanning
```http
POST /dashboard/ticket-scan/verify-ticket
```
Verify ticket using QR code.

```http
POST /dashboard/ticket-scan/get-booking
```
Get booking information for scanning.

### Special Integrations

#### AI Content Generation
```http
POST /ai/generate
```
Generate AI-powered content for events.

#### OAuth Authentication
```http
GET /login/{social}
```
Initiate OAuth login (Facebook/Google).

```http
GET /login/{social}/callback
```
Handle OAuth callback.

#### Bulk Bookings
```http
GET /bookings/bulk
POST /bookings/bulk
```
Manage bulk bookings.

```http
GET /bookings/bulk/edit/{id}
PUT /bookings/bulk/update/{id}
DELETE /bookings/bulk/delete/{id}
```
CRUD operations for bulk bookings.

```http
GET /bookings/bulk/show/{id}
```
Show bulk booking details.

```http
GET /bookings/bulk/zip/{ticket_id}/{bulk_code}
```
Download bulk booking tickets as ZIP.

```http
GET /bookings/bulk/export/{ticket_id}/{bulk_code}
```
Export bulk booking attendees.

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