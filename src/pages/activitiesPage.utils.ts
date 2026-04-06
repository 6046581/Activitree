export type ActivitySearchParams = {
   id?: string;
   time?: string;
   create?: string;
};

export type ApiResponse<T> = {
   data?: T;
   error?: string;
};

export type ApiActivity = {
   id: number;
   title: string;
   description?: string;
   activity_type: "indoor" | "outdoor";
   status: "planned" | "cancelled" | "completed";
   activity_time: string;
   location_id: number | null;
   photo_path?: string | null;
   photo_url?: string | null;
   created_by: number;
   created_by_username?: string | null;
   participant_ids?: number[];
};

export type ApiLocation = {
   id: number;
   latitude: number;
   longitude: number;
   city?: string | null;
   street?: string | null;
   house_number?: string | null;
   formatted_address?: string | null;
};

export type ApiParticipant = {
   id: number;
   username: string;
   email: string;
   avatar_path?: string | null;
   avatar_url?: string | null;
   joined_at?: string;
};

export type UiActivity = {
   id: number;
   title: string;
   description: string;
   type: "indoor" | "outdoor";
   status: "planned" | "cancelled" | "completed";
   date: string;
   time: string;
   locationId: number | null;
   photoPath: string | null;
   photoUrl: string | null;
   createdBy: number;
   createdByUsername: string;
   participantIds: number[];
};

export type UiLocation = {
   id: number;
   name: string;
   address: string;
   latitude: number;
   longitude: number;
};

export type UiParticipant = {
   id: number;
   username: string;
   email: string;
   avatar: string;
   joinedAt: string;
};

const AVATAR_PLACEHOLDER = "/assets/avatar_placeholder.jpg";

function normalizeAvatarPath(pathValue: string): string {
   const clean = pathValue.trim();
   if (!clean) {
      return "";
   }

   if (clean.startsWith("http://") || clean.startsWith("https://")) {
      return clean;
   }

   return clean.startsWith("/") ? clean : `/${clean}`;
}

export function resolveAvatarUrl(avatarUrl?: string | null, avatarPath?: string | null, id?: number | null): string {
   const fromUrl = typeof avatarUrl === "string" ? normalizeAvatarPath(avatarUrl) : "";
   if (fromUrl) {
      return fromUrl;
   }

   const fromPath = typeof avatarPath === "string" ? normalizeAvatarPath(avatarPath) : "";
   if (fromPath) {
      return fromPath;
   }

   const parsedId = Number(id);
   if (Number.isFinite(parsedId) && parsedId > 0) {
      return `/api/uploads/avatars/sample_${parsedId}.jpg`;
   }

   return AVATAR_PLACEHOLDER;
}

function splitActivityTime(activityTime: string): { date: string; time: string } {
   const normalized = activityTime.replace("T", " ").trim();
   const [datePart = "", timePart = ""] = normalized.split(" ");
   return {
      date: datePart,
      time: timePart.slice(0, 5),
   };
}

export function mapActivity(row: ApiActivity): UiActivity {
   const { date, time } = splitActivityTime(String(row.activity_time ?? ""));
   const participantIds = Array.isArray(row.participant_ids) ? row.participant_ids.map((id) => Number(id)).filter((id) => Number.isFinite(id)) : [];

   return {
      id: Number(row.id),
      title: String(row.title ?? "Untitled activity"),
      description: String(row.description ?? ""),
      type: row.activity_type,
      status: row.status,
      date,
      time,
      locationId: row.location_id == null ? null : Number(row.location_id),
      photoPath: row.photo_path == null ? null : String(row.photo_path),
      photoUrl: row.photo_url == null ? null : String(row.photo_url),
      createdBy: Number(row.created_by),
      createdByUsername: String(row.created_by_username ?? "Unknown host"),
      participantIds,
   };
}

export function mapLocation(row: ApiLocation): UiLocation {
   const streetLine = [row.street, row.house_number].filter(Boolean).join(" ").trim();
   const address = String(row.formatted_address ?? "").trim() || streetLine || String(row.city ?? "").trim() || "Unknown location";
   const name = String(row.city ?? "").trim() || address;

   return {
      id: Number(row.id),
      name,
      address,
      latitude: Number(row.latitude),
      longitude: Number(row.longitude),
   };
}

export async function parseApiData<T>(response: Response): Promise<T> {
   const json: unknown = await response.json().catch(() => ({}));
   if (json !== null && typeof json === "object") {
      const payload = json as ApiResponse<T>;
      if (payload.data !== undefined) {
         return payload.data as T;
      }
   }
   return json as T;
}

export function extractRows<T>(payload: ApiResponse<T[]> | T[]): T[] {
   if (Array.isArray(payload)) {
      return payload;
   }

   return Array.isArray(payload?.data) ? payload.data : [];
}

export function mapParticipant(row: ApiParticipant): UiParticipant {
   const id = Number(row.id);
   const username = String(row.username ?? "Unknown");

   return {
      id,
      username,
      email: String(row.email ?? ""),
      avatar: resolveAvatarUrl(row.avatar_url, row.avatar_path, id),
      joinedAt: String(row.joined_at ?? ""),
   };
}

export async function parseApiError(response: Response, fallbackMessage: string): Promise<string> {
   try {
      const json = (await response.json()) as ApiResponse<unknown>;
      if (json && typeof json.error === "string" && json.error.trim().length > 0) {
         return json.error;
      }
   } catch {
      // Ignore JSON parsing errors and use fallback message.
   }

   return fallbackMessage;
}

export function filterActivitiesByMode(activities: UiActivity[], mode: string | undefined, currentUserId: number | null): UiActivity[] {
   const normalizedMode = String(mode ?? "").toLowerCase();

   if (!normalizedMode || normalizedMode === "discover") {
      return activities;
   }

   if (normalizedMode === "attending") {
      if (currentUserId == null) {
         return [];
      }
      return activities.filter((activity) => activity.participantIds.includes(currentUserId));
   }

   if (normalizedMode === "organizing") {
      if (currentUserId == null) {
         return [];
      }
      return activities.filter((activity) => activity.createdBy === currentUserId);
   }

   return activities.filter((activity) => activity.type === normalizedMode || activity.status === normalizedMode);
}
