const USER_STORAGE_KEY = "user";
const TOKEN_STORAGE_KEY = "token";

import { notyf } from "./notyf";

export type AuthUser = {
   id: number;
   username: string;
   email: string;
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

export function isLoggedIn(): boolean {
   return !!readStoredValue(TOKEN_STORAGE_KEY);
}

export function login(token: string, user?: AuthUser, remember = true) {
   clearStoredValue(TOKEN_STORAGE_KEY);
   clearStoredValue(USER_STORAGE_KEY);

   const storage = getStorage(remember);
   storage.setItem(TOKEN_STORAGE_KEY, token);

   if (user) {
      storage.setItem(USER_STORAGE_KEY, JSON.stringify(user));
   }

   notyf.success("Logged in.");
}

export function logout() {
   clearStoredValue(TOKEN_STORAGE_KEY);
   clearStoredValue(USER_STORAGE_KEY);
}

export function getToken(): string | null {
   return readStoredValue(TOKEN_STORAGE_KEY);
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
