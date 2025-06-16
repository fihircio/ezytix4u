# Database Schema Documentation

## Overview
This document describes the database schema for the Event Portal system. The database uses MySQL and follows Laravel's migration system.

## Tables

### users
Stores user account information.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar(255) | User's full name |
| email | varchar(255) | User's email address |
| password | varchar(255) | Hashed password |
| role | enum('admin','organizer','user') | User role |
| created_at | timestamp | Record creation timestamp |
| updated_at | timestamp | Record update timestamp |

### events
Stores event information.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| title | varchar(255) | Event title |
| description | text | Event description |
| start_date | datetime | Event start date and time |
| end_date | datetime | Event end date and time |
| venue | varchar(255) | Event venue |
| category_id | bigint | Foreign key to categories |
| organizer_id | bigint | Foreign key to users |
| ticket_price | decimal(10,2) | Price per ticket |
| total_tickets | int | Total available tickets |
| created_at | timestamp | Record creation timestamp |
| updated_at | timestamp | Record update timestamp |

### categories
Stores event categories.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar(255) | Category name |
| description | text | Category description |
| created_at | timestamp | Record creation timestamp |
| updated_at | timestamp | Record update timestamp |

### tickets
Stores ticket purchase information.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| ticket_number | varchar(255) | Unique ticket number |
| event_id | bigint | Foreign key to events |
| user_id | bigint | Foreign key to users |
| quantity | int | Number of tickets |
| total_amount | decimal(10,2) | Total amount paid |
| status | enum('pending','paid','cancelled') | Ticket status |
| created_at | timestamp | Record creation timestamp |
| updated_at | timestamp | Record update timestamp |

### payments
Stores payment information.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| ticket_id | bigint | Foreign key to tickets |
| amount | decimal(10,2) | Payment amount |
| payment_method | varchar(50) | Payment method used |
| status | enum('pending','completed','failed') | Payment status |
| transaction_id | varchar(255) | Payment gateway transaction ID |
| created_at | timestamp | Record creation timestamp |
| updated_at | timestamp | Record update timestamp |

## Relationships

### One-to-Many
- User (1) -> Events (N)
- User (1) -> Tickets (N)
- Category (1) -> Events (N)
- Event (1) -> Tickets (N)
- Ticket (1) -> Payments (N)

## Indexes

### Primary Keys
- users: id
- events: id
- categories: id
- tickets: id
- payments: id

### Foreign Keys
- events: category_id -> categories.id
- events: organizer_id -> users.id
- tickets: event_id -> events.id
- tickets: user_id -> users.id
- payments: ticket_id -> tickets.id

### Additional Indexes
- users: email (unique)
- events: start_date
- events: category_id
- tickets: ticket_number (unique)
- tickets: status
- payments: transaction_id (unique)

## Data Integrity

### Constraints
1. Event dates must be valid (end_date > start_date)
2. Ticket quantity must be positive
3. Payment amount must match ticket total
4. User email must be unique
5. Ticket numbers must be unique

### Cascading Deletes
- When an event is deleted, all associated tickets are deleted
- When a ticket is deleted, all associated payments are deleted

## Backup and Recovery
- Daily automated backups
- Point-in-time recovery available
- Backup retention: 30 days

## Performance Considerations
1. Indexes on frequently queried columns
2. Partitioning for large tables (events, tickets)
3. Regular maintenance and optimization
4. Monitoring of query performance 