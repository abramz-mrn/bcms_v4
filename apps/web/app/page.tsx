export default function Home() {
  return (
    <main className="max-w-4xl mx-auto p-8">
      <div className="rounded-2xl bg-white shadow p-6 border">
        <h1 className="text-2xl font-semibold">BCMS v4 Admin</h1>
        <p className="mt-2 text-slate-600">
          Starter dashboard berjalan. Next step: login UI + pages modul CRUD.
        </p>
        <div className="mt-6 grid grid-cols-2 gap-4">
          <a className="p-4 rounded-xl border bg-slate-50" href="/login">Login</a>
          <a className="p-4 rounded-xl border bg-slate-50" href="/health">Health</a>
        </div>
      </div>
    </main>
  );
}