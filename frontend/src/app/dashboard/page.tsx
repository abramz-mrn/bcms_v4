import { apiGet } from "@/lib/api";

export default async function Dashboard() {
  const me = await apiGet("/auth/me", { credentials: "include" });

  return (
    <main className="p-6 space-y-4">
      <h1 className="text-2xl font-semibold">Dashboard</h1>
      <div className="bg-white border rounded p-4">
        <h2 className="font-medium">Me</h2>
        <pre className="text-sm mt-2">{JSON.stringify(me, null, 2)}</pre>
      </div>
    </main>
  );
}