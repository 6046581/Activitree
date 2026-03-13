export function isLoggedIn(): boolean {
  return !!localStorage.getItem("authToken");
}

export function login(token: string) {
  localStorage.setItem("authToken", token);
}

export function logout() {
  localStorage.removeItem("authToken");
}

export function getToken(): string | null {
  return localStorage.getItem("authToken");
}