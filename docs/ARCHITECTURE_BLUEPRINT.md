# BCMS v4 — Architecture & Blueprint

## Components
- Nginx: reverse proxy `/` -> Next.js, `/api` -> Laravel
- Next.js (SSR): admin panel
- Laravel API: Sanctum, RBAC, modules
- PostgreSQL: main DB
- Redis: cache + queue
- Horizon: queue monitoring
- Scheduler: artisan schedule runner

## Core Domains
- Master: companies, brands, products, promotions, routers, templates
- CRM: customers, subscriptions, provisionings
- Billing: invoices, payments, reminders
- Support: tickets + SLA
- Audit: audit_logs

## Integrations (planned adapters)
- Mikrotik: RouterOS API via TLS + fallback SSH
- Payment gateways: Midtrans/Xendit webhook -> payments + invoices
- Notifications: SMTP + SMS gateway + WhatsApp Business Cloud API

## Automation
- Invoice generator: H-7 from period start per billing cycle
- Reminder engine: H-7/H-3/H-1/H+1 + pre-soft-limit + pre-suspend
- Auto soft-limit: bandwidth drop 50% after `internet_services.auto_soft_limit` days past due
- Auto suspend: suspend after `internet_services.auto_suspend` days past due
- Provisioning job: idempotent, retry/backoff

## Security
- Sanctum SPA cookie (recommended for Next.js SSR admin)
- RBAC permission JSON on group
- Audit log for critical actions
- Rate limiting for auth and webhooks

## Localhost networking
- Nginx: 80
- Web: 3000
- API: 8000 (internal), routed by Nginx `/api`
- Postgres: 5432
- Redis: 6379