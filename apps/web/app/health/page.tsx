export default async function Health() {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";
  const res = await fetch(`${base}/v1/auth/me`, {
    cache: "no-store",
    credentials: "include"
  });

  return (
    <main className="max-w-2xl mx-auto p-8">
      <h1 className="text-xl font-semibold">Health</h1>
      <p className="mt-2">API reachable: {String(res.status)}</p>
      <pre className="mt-4 p-4 bg-white border rounded-xl overflow-auto">
        {await res.text()}
      </pre>
    </main>
  );
}