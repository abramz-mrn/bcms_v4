const base = process.env.NEXT_PUBLIC_API_BASE || "http://127.0.0.1:8080/api";

export async function apiFetch(path: string, init?: RequestInit) {
  const res = await fetch(`${base}${path}`, {
    ...init,
    headers: { ...(init?.headers || {}) },
    cache: "no-store",
  });

  if (!res.ok) {
    const text = await res.text().catch(() => "");
    throw new Error(`${init?.method || "GET"} ${path} failed: ${res.status} ${text}`);
  }

  const ct = res.headers.get("content-type") || "";
  if (ct.includes("application/json")) return res.json();
  return res.text();
}

export async function apiGet(path: string, init?: RequestInit) {
  return apiFetch(path, { ...init, method: "GET" });
}

export async function apiPost(path: string, body: any, init?: RequestInit) {
  return apiFetch(path, {
    ...init,
    method: "POST",
    headers: { "Content-Type": "application/json", ...(init?.headers || {}) },
    body: JSON.stringify(body),
  });
}

export async function apiPut(path: string, body: any, init?: RequestInit) {
  return apiFetch(path, {
    ...init,
    method: "PUT",
    headers: { "Content-Type": "application/json", ...(init?.headers || {}) },
    body: JSON.stringify(body),
  });
}

export async function apiDelete(path: string, init?: RequestInit) {
  return apiFetch(path, { ...init, method: "DELETE" });
}

export async function ensureCsrfCookie(): Promise<void> {
  await fetch(`${base}/csrf-cookie`, { method: "GET", credentials: "include" });
}