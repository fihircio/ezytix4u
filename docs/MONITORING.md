# Monitoring and Alerting Guide

## Overview
This guide outlines the monitoring and alerting system for the Event Portal, ensuring system health, performance, and security.

## Monitoring Components

### System Metrics
- CPU Usage
- Memory Utilization
- Disk Space
- Network Traffic
- Process Status

### Application Metrics
- Response Times
- Error Rates
- Request Volume
- Queue Lengths
- Cache Hit Rates

### Database Metrics
- Query Performance
- Connection Pool
- Replication Lag
- Table Sizes
- Index Usage

### Business Metrics
- Active Users
- Ticket Sales
- Transaction Volume
- Event Creation Rate
- Payment Success Rate

## Alerting Thresholds

### Critical Alerts
```yaml
# System Alerts
cpu_usage:
  warning: 80%
  critical: 90%
  duration: 5m

memory_usage:
  warning: 85%
  critical: 95%
  duration: 5m

disk_space:
  warning: 85%
  critical: 95%
  duration: 1h

# Application Alerts
response_time:
  warning: 500ms
  critical: 1000ms
  duration: 5m

error_rate:
  warning: 1%
  critical: 5%
  duration: 5m

# Database Alerts
query_time:
  warning: 1s
  critical: 5s
  duration: 5m

replication_lag:
  warning: 30s
  critical: 60s
  duration: 5m
```

### Warning Alerts
```yaml
# Performance Alerts
cache_hit_rate:
  warning: 80%
  critical: 60%
  duration: 15m

queue_length:
  warning: 1000
  critical: 5000
  duration: 10m

# Business Alerts
payment_failure_rate:
  warning: 2%
  critical: 5%
  duration: 15m

ticket_sales_rate:
  warning: -50% from average
  critical: -80% from average
  duration: 1h
```

## Monitoring Tools

### System Monitoring
```bash
# Example: System health check script
#!/bin/bash

check_cpu() {
    cpu_usage=$(top -bn1 | grep "Cpu(s)" | awk '{print $2}')
    if [ $(echo "$cpu_usage > 90" | bc) -eq 1 ]; then
        alert "CPU usage critical: $cpu_usage%"
    fi
}

check_memory() {
    memory_usage=$(free | grep Mem | awk '{print $3/$2 * 100.0}')
    if [ $(echo "$memory_usage > 95" | bc) -eq 1 ]; then
        alert "Memory usage critical: $memory_usage%"
    fi
}

check_disk() {
    disk_usage=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')
    if [ $disk_usage -gt 95 ]; then
        alert "Disk usage critical: $disk_usage%"
    fi
}
```

### Application Monitoring
```php
// Example: Application health check
namespace App\Services;

class HealthCheck
{
    public function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            $this->alert('Database connection failed');
            return false;
        }
    }

    public function checkRedis()
    {
        try {
            Redis::ping();
            return true;
        } catch (\Exception $e) {
            $this->alert('Redis connection failed');
            return false;
        }
    }

    public function checkQueue()
    {
        $queueLength = Queue::size('default');
        if ($queueLength > 5000) {
            $this->alert("Queue length critical: $queueLength");
        }
    }
}
```

### Log Monitoring
```php
// Example: Log monitoring configuration
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'slack'],
        'ignore_exceptions' => false,
    ],
    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => 'Event Portal Logger',
        'emoji' => ':boom:',
        'level' => 'critical',
    ],
]
```

## Alerting Channels

### Email Alerts
```php
// Example: Email alert configuration
'mail' => [
    'driver' => 'smtp',
    'host' => env('MAIL_HOST'),
    'port' => env('MAIL_PORT'),
    'from' => [
        'address' => 'alerts@eventportal.com',
        'name' => 'Event Portal Alerts'
    ],
    'to' => [
        'admin@eventportal.com',
        'ops@eventportal.com'
    ]
]
```

### Slack Integration
```php
// Example: Slack notification
public function sendSlackAlert($message, $level = 'warning')
{
    $color = $level === 'critical' ? '#FF0000' : '#FFA500';
    
    Slack::to('#alerts')->attach([
        'color' => $color,
        'title' => "Event Portal Alert: $level",
        'text' => $message,
        'ts' => time()
    ]);
}
```

### SMS Alerts
```php
// Example: SMS alert configuration
'sms' => [
    'provider' => 'twilio',
    'from' => env('SMS_FROM'),
    'to' => [
        '+1234567890', // Primary on-call
        '+0987654321'  // Secondary on-call
    ]
]
```

## Incident Response

### Severity Levels
1. **Critical (P0)**
   - System down
   - Data loss
   - Security breach
   - Response: Immediate

2. **High (P1)**
   - Major feature unavailable
   - Performance degradation
   - Response: Within 1 hour

3. **Medium (P2)**
   - Minor feature issues
   - Non-critical errors
   - Response: Within 4 hours

4. **Low (P3)**
   - Cosmetic issues
   - Minor bugs
   - Response: Within 24 hours

### Response Procedures
```markdown
1. Alert Received
   - Acknowledge alert
   - Assess severity
   - Notify appropriate team

2. Initial Response
   - Gather information
   - Check monitoring dashboards
   - Review recent changes

3. Investigation
   - Check logs
   - Review metrics
   - Identify root cause

4. Resolution
   - Implement fix
   - Verify resolution
   - Update status

5. Post-Incident
   - Document incident
   - Update runbooks
   - Schedule review
```

## Monitoring Dashboard

### Key Metrics
- System Health
- Application Performance
- Business Metrics
- Error Rates
- User Activity

### Custom Views
- Operations Dashboard
- Business Dashboard
- Security Dashboard
- Performance Dashboard

## Maintenance Procedures

### Daily Checks
- [ ] Review alert history
- [ ] Check system metrics
- [ ] Monitor error rates
- [ ] Review performance

### Weekly Tasks
- [ ] Analyze trends
- [ ] Review thresholds
- [ ] Update documentation
- [ ] Check capacity

### Monthly Reviews
- [ ] Performance analysis
- [ ] Alert tuning
- [ ] Capacity planning
- [ ] Update procedures

## Best Practices

### Monitoring
1. Monitor everything
2. Set meaningful thresholds
3. Use multiple channels
4. Regular review
5. Document procedures

### Alerting
1. Avoid alert fatigue
2. Use appropriate severity
3. Include context
4. Provide action items
5. Regular tuning

### Response
1. Clear procedures
2. Regular drills
3. Document incidents
4. Learn from issues
5. Update runbooks 