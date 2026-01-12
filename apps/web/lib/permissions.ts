export type Permissions = Record<string, boolean>;

export function hasPermission(perms: Permissions | undefined | null, key: string) {
  if (!perms) return false;
  if (perms["*"] === true) return true;
  return perms[key] === true;
}