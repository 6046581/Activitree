/**
 * Returns a Google Calendar event link for the given activity.
 * @param title - Event title
 * @param description - Event description
 * @param locationLabel - Location string
 * @param dateStr - Date string in YYYY-MM-DD format
 * @param timeStr - Time string in HH:mm format
 * @param durationHours - Duration in hours (default 2)
 */
export function getGoogleCalendarLink(
   title: string,
   description: string,
   locationLabel: string,
   dateStr?: string,
   timeStr?: string,
   durationHours = 2
): string {
   const enc = encodeURIComponent;
   if (dateStr && timeStr) {
      const [yearStr, monthStr, dayStr] = dateStr.split("-");
      const [hourStr, minuteStr] = timeStr.split(":");
      const year = Number(yearStr),
         month = Number(monthStr),
         day = Number(dayStr);
      const hour = Number(hourStr),
         minute = Number(minuteStr);
      if (!isNaN(year) && !isNaN(month) && !isNaN(day) && !isNaN(hour) && !isNaN(minute)) {
         const start = new Date(year, month - 1, day, hour, minute);
         const end = new Date(start.getTime() + durationHours * 60 * 60 * 1000);
         const pad = (n: number) => n.toString().padStart(2, "0");
         const fmt = (d: Date) => `${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}T${pad(d.getHours())}${pad(d.getMinutes())}00Z`;
         return `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${enc(title)}&details=${enc(description)}&location=${enc(locationLabel)}&dates=${fmt(start)}/${fmt(end)}`;
      }
   }
   return "https://calendar.google.com";
}

/**
 * Returns a time-of-day Unsplash image URL based on the hour (0-23).
 * @param hour - Hour of day (0-23)
 */
export function getTimeOfDayImage(hour?: number): string {
   if (typeof hour !== "number" || isNaN(hour)) return "/assets/placeholder.jpg";
   if (hour >= 6 && hour < 12) {
      // Morning
      return "/assets/time/sunrise.jpg";
   } else if (hour >= 12 && hour < 18) {
      // Afternoon
      return "/assets/time/midday.jpg";
   } else if (hour >= 18 && hour < 21) {
      // Evening
      return "/assets/time/sunset.jpg";
   } else {
      // Night
      return "/assets/time/night.jpg";
   }
}
// src/dateLabel.ts
// Utility to format activity date labels for ActivityPopover

/**
 * Returns a human-friendly label for an activity date.
 * - Today: "Today"
 * - Within 7 days: weekday name (e.g., "Saturday")
 * - In the future: "in X days/weeks/months"
 * - In the past: "Activity has passed"
 *
 * @param dateStr - Date string in YYYY-MM-DD format
 * @param now - Optional Date object for testing/overriding current time
 * @returns string
 */
export function getDateLabel(dateStr: string, now: Date = new Date()): string {
   if (!dateStr) return "";
   const [yearStr, monthStr, dayStr] = dateStr.split("-");
   const year = Number(yearStr),
      month = Number(monthStr),
      day = Number(dayStr);
   if (isNaN(year) || isNaN(month) || isNaN(day)) return "";
   const activityDate = new Date(year, month - 1, day);
   // Remove time for comparison
   activityDate.setHours(0, 0, 0, 0);
   const today = new Date(now);
   today.setHours(0, 0, 0, 0);
   const diffMs = activityDate.getTime() - today.getTime();
   const diffDays = Math.round(diffMs / (1000 * 60 * 60 * 24));

   if (diffDays === 0) {
      return "Today";
   } else if (diffDays > 0 && diffDays < 7) {
      // Within 7 days
      return activityDate.toLocaleDateString(undefined, { weekday: "long" });
   } else if (diffDays > 0) {
      if (diffDays < 30) {
         return `In ${diffDays} day${diffDays === 1 ? "" : "s"}`;
      } else if (diffDays < 60) {
         return "In 1 month";
      } else if (diffDays < 365) {
         const months = Math.round(diffDays / 30);
         return `In ${months} month${months === 1 ? "" : "s"}`;
      } else {
         const years = Math.round(diffDays / 365);
         return `In ${years} year${years === 1 ? "" : "s"}`;
      }
   } else {
      return "Activity has passed";
   }
}
