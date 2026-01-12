import Link from "next/link";

const nav = [
  { href: "/admin", label: "Dashboard" },
  { href: "/admin/customers", label: "Customers" },
  { href: "/admin/subscriptions", label: "Subscriptions" },
  { href: "/admin/provisionings", label: "Provisionings" },

  { href: "/admin/invoices", label: "Invoices" },
  { href: "/admin/payments", label: "Payments" },
  { href: "/admin/reminders", label: "Reminders" },

  { href: "/admin/tickets", label: "Tickets" },
  { href: "/admin/products", label: "Products" },
  { href: "/admin/routers", label: "Routers" },
  { href: "/admin/brands", label: "Brands" },
  { href: "/admin/companies", label: "Companies" },
  { href: "/admin/templates", label: "Templates" },
];

export default function AdminShell({
  title,
  userLabel,
  children,
}: {
  title: string;
  userLabel: string;
  children: React.ReactNode;
}) {
  return (
    <div className="min-h-screen flex">
      <aside className="w-64 bg-slate-900 text-slate-100">
        <div className="px-4 py-4 border-b border-slate-800">
          <div className="font-semibold">BCMS v4</div>
          <div className="text-xs text-slate-400">Classic Admin</div>
        </div>

        <div className="px-4 py-3 text-xs text-slate-400">Logged as</div>
        <div className="px-4 pb-3 text-sm">{userLabel}</div>

        <nav className="px-2 py-2 space-y-1">
          {nav.map((n) => (
            <Link
              key={n.href}
              href={n.href}
              className="block px-3 py-2 rounded hover:bg-slate-800 text-sm"
            >
              {n.label}
            </Link>
          ))}
        </nav>

        <div className="mt-auto px-4 py-4 border-t border-slate-800">
          <form action="/logout" method="post">
            <button className="w-full px-3 py-2 rounded bg-slate-800 hover:bg-slate-700 text-sm">
              Logout
            </button>
          </form>
        </div>
      </aside>

      <div className="flex-1 bg-slate-50">
        <header className="bg-white border-b">
          <div className="px-6 py-4">
            <h1 className="text-xl font-semibold">{title}</h1>
          </div>
        </header>

        <main className="p-6">{children}</main>
      </div>
    </div>
  );
}