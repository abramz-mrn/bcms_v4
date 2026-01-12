<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        // Total pelanggan: customer unik yang punya subscription status != Terminated
        $totalCustomers = Customer::query()
            ->whereHas('subscriptions', function ($q) {
                $q->where('status', '!=', 'Terminated');
            })
            ->distinct('customers.id')
            ->count('customers.id');

        // Total pelanggan aktif: customer unik yang punya minimal 1 subscription
        // yang status-nya bukan Suspend dan bukan Terminated
        $totalActiveCustomers = Customer::query()
            ->whereHas('subscriptions', function ($q) {
                $q->whereNotIn('status', ['Suspend', 'Terminated']);
            })
            ->distinct('customers.id')
            ->count('customers.id');

        // List pelanggan suspend (customer unik yang punya minimal 1 subscription Suspend)
        $suspendedCustomers = Customer::query()
            ->select(['customers.id','customers.code','customers.name','customers.phone','customers.email','customers.city','customers.state','customers.group_area'])
            ->whereHas('subscriptions', function ($q) {
                $q->where('status', 'Suspend');
            })
            ->orderBy('customers.name')
            ->limit(10)
            ->get();

        // Recent activity (audit logs)
        $recentActivities = AuditLog::query()
            ->select(['id','users_id','users_name','ip_address','action','resource_type','description','created_at'])
            ->latest()
            ->limit(10)
            ->get();

        // Tickets open-ish + SLA due soon
        $openTickets = Ticket::query()
            ->whereIn('status', ['open','assigned','in progress'])
            ->orderByRaw("sla_due_date asc nulls last")
            ->limit(10)
            ->get([
                'id','ticket_number','customers_id','products_id','category','priority','subject','status','sla_due_date','created_at'
            ]);

        $slaDueSoonCount = Ticket::query()
            ->whereIn('status', ['open','assigned','in progress'])
            ->whereNotNull('sla_due_date')
            ->where('sla_due_date', '<=', now()->addHours(24))
            ->count();

        return response()->json([
            'meta' => [
                'generated_at' => now()->toIso8601String(),
                'definition' => [
                    'customers_total' => 'distinct customers that have >=1 subscription with status != Terminated',
                    'customers_active' => 'distinct customers that have >=1 subscription with status NOT IN (Suspend, Terminated)',
                ],
            ],
            'totals' => [
                'customers' => $totalCustomers,
                'active_customers' => $totalActiveCustomers,
                'sla_due_soon_tickets' => $slaDueSoonCount,
            ],
            'lists' => [
                'suspended_customers' => $suspendedCustomers,
                'recent_activities' => $recentActivities,
                'open_tickets' => $openTickets,
            ],
        ]);
    }
}