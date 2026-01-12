# BCMS v4 - Architecture

## Runtime (Localhost)
Nginx routes:
- `/` => Next.js (SSR)
- `/api/*` => Laravel 12 (Octane RoadRunner)
- `/horizon/*` => Laravel Horizon

Services:
- PostgreSQL 18: primary DB
- Redis 8: cache + queue
- Scheduler container: runs `php artisan schedule:run` every minute
- Horizon: queue monitoring

## Domains
- Master: companies, brands, products, promotions, routers, templates
- CRM: customers, subscriptions, provisionings
- Billing: invoices, payments, reminders
- Support: tickets (+ SLA fields)
- Audit: audit_logs via middleware + service

## Integration Patterns
- Mikrotik: RouterOS API via TLS (primary) + optional SSH fallback (services + jobs are stubbed)
- Payment gateway: webhook endpoints (stub) -> update payment & invoice
- Notifications: email (Laravel mail) + SMS/WA driver interface + dummy driver

## Automation (scheduler/queue skeleton)
- Invoice generator: create invoices by billing cycle
- Reminder engine: create reminder schedules H-7/H-3/H-1/H/H+1/H+3 + pre-soft-limit + pre-suspend
- Soft-limit: reduce bandwidth 50%
- Suspend: disable services
- Reactivate: restore after payment