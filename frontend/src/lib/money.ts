export function parseRupiahDecimalToInt(input: string): number {
  // Accept: "150000.00", "150000", "150.000,00" (basic), "150,000.00" (basic)
  const s = String(input ?? "")
    .trim()
    .replace(/\s/g, "")
    .replace(/Rp/gi, "");

  if (!s) return 0;

  // If contains both '.' and ',', assume one is thousand separator.
  // We normalize by removing thousand separators, keeping decimal point.
  // Simple approach for dev: remove commas, then remove dots if more than 1 dot.
  let normalized = s.replace(/,/g, "");
  const dotCount = (normalized.match(/\./g) || []).length;
  if (dotCount > 1) normalized = normalized.replace(/\./g, "");

  const n = Number.parseFloat(normalized);
  if (Number.isNaN(n)) return 0;

  return Math.round(n);
}