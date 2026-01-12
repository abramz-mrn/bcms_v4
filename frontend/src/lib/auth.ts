import { cookies } from "next/headers";

const apiBase = process.env.NEXT_PUBLIC_API_BASE || "http://127.0.0.1:8080/api";

export async function requireMe() {
  const cookieStore = await cookies();
  const cookieHeader = cookieStore
    .getAll()
    .map((c) => `${c.name}=${c.value}`)
    .join("; ");

  const res = await fetch(`${apiBase}/auth/me`, {
    method: "GET",
    headers: { Cookie: cookieHeader },
    cache: "no-store",
  });

  if (!res.ok) return null;
  return res.json();
}