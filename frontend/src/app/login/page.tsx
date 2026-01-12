"use client";

import { useState } from "react";
import { apiPost, ensureCsrfCookie } from "@/lib/api";

export default function LoginPage() {
  const [email, setEmail] = useState("abramz@maroon-net.id");
  const [password, setPassword] = useState("PassWord@123");
  const [msg, setMsg] = useState<string>("");

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setMsg("");

    try {
      await ensureCsrfCookie();
      await apiPost("/auth/login", { email, password }, { credentials: "include" });
      window.location.href = "/admin";
    } catch (err: any) {
      setMsg(err?.message ?? "Login failed");
    }
  }

  return (
    <main className="min-h-screen flex items-center justify-center p-6 bg-slate-50">
      <form onSubmit={onSubmit} className="w-full max-w-md bg-white rounded-xl shadow p-6 space-y-4 border">
        <h1 className="text-2xl font-semibold">BCMS Login</h1>
        <p className="text-sm text-slate-600">Use seeded account for local dev.</p>

        <div className="space-y-2">
          <label className="text-sm">Email</label>
          <input className="w-full border rounded px-3 py-2" value={email} onChange={(e) => setEmail(e.target.value)} />
        </div>

        <div className="space-y-2">
          <label className="text-sm">Password</label>
          <input className="w-full border rounded px-3 py-2" type="password" value={password} onChange={(e) => setPassword(e.target.value)} />
        </div>

        <button className="w-full px-4 py-2 rounded bg-slate-900 text-white hover:bg-slate-800">
          Login
        </button>

        {msg && <p className="text-sm text-red-600">{msg}</p>}
      </form>
    </main>
  );
}