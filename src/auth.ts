import { notyf } from "./notyf";

const USER_STORAGE_KEY = "user";
const TOKEN_STORAGE_KEY = "token";
const TOKEN_COOKIE_KEY = "activitree_token";
const TOKEN_COOKIE_MAX_AGE_SECONDS = 60 * 60 * 24 * 30; // 30 days

export type AuthUser = {
   id: number;
   username: string;
   email: string;
   role?: "admin" | "user";
   created_at?: string;
};

function getStorage(remember: boolean): Storage {
   return remember ? localStorage : sessionStorage;
}

function readStoredValue(key: string): string | null {
   return localStorage.getItem(key) ?? sessionStorage.getItem(key);
}

function clearStoredValue(key: string) {
   localStorage.removeItem(key);
   sessionStorage.removeItem(key);
}

function setCookie(name: string, value: string, maxAgeSeconds: number) {
   document.cookie = `${encodeURIComponent(name)}=${encodeURIComponent(value)}; path=/; max-age=${maxAgeSeconds}; samesite=lax`;
}

function clearCookie(name: string) {
   document.cookie = `${encodeURIComponent(name)}=; path=/; max-age=0; samesite=lax`;
}

function getCookie(name: string): string | null {
   const encodedName = `${encodeURIComponent(name)}=`;
   const parts = document.cookie.split(";");

   for (const part of parts) {
      const trimmed = part.trim();
      if (trimmed.startsWith(encodedName)) {
         return decodeURIComponent(trimmed.slice(encodedName.length));
      }
   }

   return null;
}

export function isLoggedIn(): boolean {
   return !!getToken() && !!getCurrentUser()?.id;
}

export function login(token: string, user?: AuthUser, remember = true) {
   clearStoredValue(TOKEN_STORAGE_KEY);
   clearStoredValue(USER_STORAGE_KEY);
   clearCookie(TOKEN_COOKIE_KEY);

   if (remember) {
      setCookie(TOKEN_COOKIE_KEY, token, TOKEN_COOKIE_MAX_AGE_SECONDS);
   } else {
      sessionStorage.setItem(TOKEN_STORAGE_KEY, token);
   }

   if (user) {
      const storage = getStorage(remember);
      storage.setItem(USER_STORAGE_KEY, JSON.stringify(user));
   }

   notyf.success("Logged in.");
}

export function logout() {
   clearStoredValue(TOKEN_STORAGE_KEY);
   clearStoredValue(USER_STORAGE_KEY);
   clearCookie(TOKEN_COOKIE_KEY);
}

export function getToken(): string | null {
   const sessionToken = sessionStorage.getItem(TOKEN_STORAGE_KEY);
   if (sessionToken) {
      return sessionToken;
   }

   const cookieToken = getCookie(TOKEN_COOKIE_KEY);
   if (cookieToken) {
      return cookieToken;
   }

   return null;
}

export function getCurrentUser(): AuthUser | null {
   const raw = readStoredValue(USER_STORAGE_KEY);
   if (!raw) {
      return null;
   }

   try {
      return JSON.parse(raw) as AuthUser;
   } catch {
      return null;
   }
}

export function getAuthHeaders(extraHeaders: Record<string, string> = {}): Record<string, string> {
   const token = getToken();
   const headers: Record<string, string> = {
      ...extraHeaders,
   };

   if (token) {
      headers["Authorization"] = `Bearer ${token}`;
   }

   return headers;
}

function upsertStoredUser(user: AuthUser) {
   const hasLocal = !!localStorage.getItem(USER_STORAGE_KEY);
   const hasSession = !!sessionStorage.getItem(USER_STORAGE_KEY);

   if (hasLocal) {
      localStorage.setItem(USER_STORAGE_KEY, JSON.stringify(user));
      return;
   }

   if (hasSession) {
      sessionStorage.setItem(USER_STORAGE_KEY, JSON.stringify(user));
      return;
   }

   localStorage.setItem(USER_STORAGE_KEY, JSON.stringify(user));
}

type ApiRequestOptions = {
   method?: string;
   body?: BodyInit | null;
   headers?: Record<string, string>;
   includeAuth?: boolean;
};

export async function apiRequest(url: string, options: ApiRequestOptions = {}): Promise<Response> {
   const { method = "GET", body = null, headers = {}, includeAuth = true } = options;
   const resolvedHeaders = includeAuth ? getAuthHeaders(headers) : headers;

   return fetch(url, {
      method,
      headers: resolvedHeaders,
      body,
   });
}

export async function apiPostJson(url: string, payload: unknown, includeAuth = false): Promise<Response> {
   return apiRequest(url, {
      method: "POST",
      includeAuth,
      headers: {
         "Content-Type": "application/json",
      },
      body: JSON.stringify(payload),
   });
}

type ProfileResponse = {
   data?: {
      id?: number;
      username?: string;
      email?: string;
      created_at?: string;
   };
};

export async function requireAuthenticatedUser(): Promise<AuthUser | null> {
   const token = getToken();
   const storedUser = getCurrentUser();

   if (!token || !storedUser?.id) {
      logout();
      return null;
   }

   try {
      const response = await apiRequest(`/api/users/${storedUser.id}`, {
         includeAuth: true,
      });

      if (!response.ok) {
         logout();
         return null;
      }

      const result = await response.json().catch(() => ({})) as ProfileResponse;
      const row = result?.data;

      if (!row?.id || !row?.username || !row?.email) {
         logout();
         return null;
      }

      const verifiedUser: AuthUser = {
         id: Number(row.id),
         username: String(row.username),
         email: String(row.email),
      };

      if (typeof row.created_at === "string") {
         verifiedUser.created_at = row.created_at;
      }

      upsertStoredUser(verifiedUser);
      return verifiedUser;
   } catch {
      logout();
      return null;
   }
}
