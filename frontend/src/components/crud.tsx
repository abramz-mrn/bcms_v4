"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";
import { apiDelete, apiPost, apiPut, ensureCsrfCookie } from "@/lib/api";

export function DangerButton({
  children,
  onClick,
}: {
  children: React.ReactNode;
  onClick: () => Promise<void> | void;
}) {
  return (
    <button
      onClick={(e) => {
        e.preventDefault();
        onClick();
      }}
      className="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700"
    >
      {children}
    </button>
  );
}

export function PrimaryButton({
  children,
  type = "button",
  onClick,
}: {
  children: React.ReactNode;
  type?: "button" | "submit";
  onClick?: () => Promise<void> | void;
}) {
  return (
    <button
      type={type}
      onClick={(e) => {
        if (!onClick) return;
        e.preventDefault();
        onClick();
      }}
      className="px-3 py-1 rounded bg-slate-800 text-white hover:bg-slate-700"
    >
      {children}
    </button>
  );
}

export function CrudMessage({ msg }: { msg: string }) {
  if (!msg) return null;
  return <div className="text-sm bg-yellow-50 border border-yellow-200 rounded p-2">{msg}</div>;
}

export function useCrudSubmit() {
  const [msg, setMsg] = useState("");
  const router = useRouter();

  return {
    msg,
    setMsg,
    async create(path: string, payload: any, redirectTo: string) {
      setMsg("");
      await ensureCsrfCookie();
      await apiPost(path, payload, { credentials: "include" });
      router.push(redirectTo);
    },
    async update(path: string, payload: any, redirectTo: string) {
      setMsg("");
      await ensureCsrfCookie();
      await apiPut(path, payload, { credentials: "include" });
      router.push(redirectTo);
    },
    async remove(path: string, redirectTo?: string) {
      setMsg("");
      await ensureCsrfCookie();
      await apiDelete(path, { credentials: "include" });
      if (redirectTo) router.push(redirectTo);
      else router.refresh();
    },
  };
}