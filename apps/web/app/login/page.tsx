"use client";

import { useState } from "react";

export default function LoginPage() {
  const [email, setEmail] = useState("abramz@maroon-net.local");
  const [password, setPassword] = useState("Password123!");
  const [token, setToken] = useState<string>("");
  const [resp, setResp] = useState<string>("");

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";

    const r = await fetch(`${base}/v1/auth/login`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password })
    });

    const t = await r.text();
    setResp(t);
    try {
      const json = JSON.parse(t);
      if (json.token) setToken(json.token);
    } catch {}
  }

  return (
    <main className="max-w-md mx-auto p-8">
      <div className="bg-white border shadow rounded-2xl p-6">
        <h1 className="text-xl font-semibold">Login</h1>
        <form onSubmit={onSubmit} className="mt-4 space-y-3">
          <div>
            <label className="text-sm text-slate-600">Email</label>
            <input className="mt-1 w-full border rounded-lg p-2"
              value={email} onChange={e=>setEmail(e.target.value)} />
          </div>
          <div>
            <label className="text-sm text-slate-600">Password</label>
            <input type="password" className="mt-1 w-full border rounded-lg p-2"
              value={password} onChange={e=>setPassword(e.target.value)} />
          </div>
          <button className="w-full bg-slate-900 text-white rounded-lg p-2">
            Sign in
          </button>
        </form>

        {token && (
          <div className="mt-4">
            <div className="text-sm font-medium">Token</div>
            <pre className="text-xs p-3 bg-slate-50 border rounded-xl overflow-auto">{token}</pre>
            <p className="text-xs text-slate-500 mt-2">
              Starter menggunakan token. Nanti bisa upgrade ke Sanctum SPA-cookie.
            </p>
          </div>
        )}

        {resp && (
          <pre className="mt-4 text-xs p-3 bg-slate-50 border rounded-xl overflow-auto">{resp}</pre>
        )}
      </div>
    </main>
  );
}