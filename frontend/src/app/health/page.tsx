import { apiGet } from "@/lib/api";

export default async function HealthPage() {
  const data = await apiGet("/health");
  return (
    <main className="p-6">
      <h1 className="text-xl font-semibold">API Health</h1>
      <pre className="mt-4 bg-white p-4 rounded border text-sm">{JSON.stringify(data, null, 2)}</pre>
    </main>
  );
}