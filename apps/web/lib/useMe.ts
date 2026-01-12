"use client";

import { useEffect, useState } from "react";

export function useMe() {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1/api";
  const [me, setMe] = useState<any>(null);

  useEffect(() => {
    (async () => {
      const r = await fetch(`${base}/v1/auth/me`, { credentials: "include", cache: "no-store" });
      if (r.ok) setMe(await r.json());
    })();
  }, [base]);

  return me?.user ?? null;
}