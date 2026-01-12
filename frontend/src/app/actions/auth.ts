"use server";

import { cookies } from "next/headers";
import { redirect } from "next/navigation";

const apiBase = process.env.NEXT_PUBLIC_API_BASE || "http://127.0.0.1:8080/api";

async function getCookieHeaderFromStore() {
  const store = await cookies();
  return store.getAll().map((c) => `${c.name}=${c.value}`).join("; ");
}

export async function logoutAction() {
  const cookieHeader = await getCookieHeaderFromStore();

  await fetch(`${apiBase}/auth/logout`, {
    method: "POST",
    headers: { Cookie: cookieHeader, "Content-Type": "application/json" },
    credentials: "include",
  });

  // clear local cookies (best effort)
  const store = await cookies();
  for (const c of store.getAll()) store.set(c.name, "", { maxAge: 0, path: "/" });

  redirect("/login");
}