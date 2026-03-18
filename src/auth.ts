const TOKEN_STORAGE_KEY = "authToken";
const USER_STORAGE_KEY = "authUser";

export type AuthUser = {
   id: number;
   username: string;
   email: string;
   role: "admin" | "user";
   created_at?: string;
};

export function isLoggedIn(): boolean {
   return !!localStorage.getItem(TOKEN_STORAGE_KEY);
}

export function login(token: string, user?: AuthUser) {
   localStorage.setItem(TOKEN_STORAGE_KEY, token);

   if (user) {
      localStorage.setItem(USER_STORAGE_KEY, JSON.stringify(user));
   }
}

export function logout() {
   localStorage.removeItem(TOKEN_STORAGE_KEY);
   localStorage.removeItem(USER_STORAGE_KEY);
}

export function getToken(): string | null {
   return localStorage.getItem(TOKEN_STORAGE_KEY);
}

export function getCurrentUser(): AuthUser | null {
   const raw = localStorage.getItem(USER_STORAGE_KEY);

   if (!raw) {
      return null;
   }

   try {
      return JSON.parse(raw) as AuthUser;
   } catch {
      return null;
   }
}
