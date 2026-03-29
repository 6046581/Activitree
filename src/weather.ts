/**
 * Returns true if the activity is outdoor and has valid location and time.
 */
export function isOutdoorActivity(activity: any, location: any): boolean {
   return (
      activity?.type === "outdoor" &&
      location?.latitude != null &&
      location?.longitude != null &&
      activity?.date &&
      activity?.time
   );
}

/**
 * Returns a met.no/yr.no weather URL for a given location.
 */
export function getWeatherUrl(location: { latitude?: number; longitude?: number }): string {
   const lat = location?.latitude;
   const lon = location?.longitude;
   if (lat && lon) {
      return `https://www.yr.no/en/forecast/graph/${lat},${lon}`;
   }
   return "https://www.yr.no/en";
}
export type WeatherResult = {
   temperature: number | null;
   windspeed: number | null;
   rainfall: number | null;
   description: string;
   icon: string;
   img: string;
   url: string;
};

const weatherMap: Record<number, { desc: string; icon: string; img: string }> = {
   0: {
      desc: "Clear sky",
      icon: "fa-sun",
      img: "/assets/weather/clear.jpg",
   },
   1: {
      desc: "Mainly clear",
      icon: "fa-cloud-sun",
      img: "/assets/weather/mainly-clear.jpg",
   },
   2: {
      desc: "Partly cloudy",
      icon: "fa-cloud",
      img: "/assets/weather/partly-cloudy.jpg",
   },
   3: {
      desc: "Overcast",
      icon: "fa-cloud",
      img: "/assets/weather/overcast.jpg",
   },
   45: {
      desc: "Fog",
      icon: "fa-smog",
      img: "/assets/weather/fog.jpg",
   },
   48: {
      desc: "Depositing rime fog",
      icon: "fa-smog",
      img: "/assets/weather/rime-fog.jpg",
   },
   51: {
      desc: "Drizzle: Light",
      icon: "fa-cloud-rain",
      img: "/assets/weather/drizzle-light.jpg",
   },
   53: {
      desc: "Drizzle: Moderate",
      icon: "fa-cloud-rain",
      img: "/assets/weather/drizzle-moderate.jpg",
   },
   55: {
      desc: "Drizzle: Dense",
      icon: "fa-cloud-showers-heavy",
      img: "/assets/weather/drizzle-dense.jpg",
   },
   61: {
      desc: "Rain: Slight",
      icon: "fa-cloud-rain",
      img: "/assets/weather/rain-slight.jpg",
   },
   63: {
      desc: "Rain: Moderate",
      icon: "fa-cloud-showers-heavy",
      img: "/assets/weather/rain-moderate.jpg",
   },
   65: {
      desc: "Rain: Heavy",
      icon: "fa-cloud-showers-heavy",
      img: "/assets/weather/rain-heavy.jpg",
   },
   71: {
      desc: "Snow: Slight",
      icon: "fa-snowflake",
      img: "/assets/weather/snow-slight.jpg",
   },
   73: {
      desc: "Snow: Moderate",
      icon: "fa-snowflake",
      img: "/assets/weather/snow-moderate.jpg",
   },
   75: {
      desc: "Snow: Heavy",
      icon: "fa-snowflake",
      img: "/assets/weather/snow-heavy.jpg",
   },
   80: {
      desc: "Rain showers: Slight",
      icon: "fa-cloud-showers-heavy",
      img: "/assets/weather/rainshowers-slight.jpg",
   },
   81: {
      desc: "Rain showers: Moderate",
      icon: "fa-cloud-showers-heavy",
      img: "/assets/weather/rainshowers-moderate.jpg",
   },
   82: {
      desc: "Rain showers: Violent",
      icon: "fa-cloud-showers-heavy",
      img: "/assets/weather/rainshowers-violent.jpg",
   },
   95: {
      desc: "Thunderstorm",
      icon: "fa-bolt",
      img: "/assets/weather/thunderstorm-slight.jpg",
   },
   96: {
      desc: "Thunderstorm with hail: Slight",
      icon: "fa-bolt",
      img: "/assets/weather/thunderstorm-hail-slight.jpg",
   },
   99: {
      desc: "Thunderstorm with hail: Heavy",
      icon: "fa-bolt",
      img: "/assets/weather/thunderstorm-hail-heavy.jpg",
   },
};

/**
 * Fetches weather data for a given latitude, longitude, date, and time using the Open-Meteo API.
 *
 * @param lat - Latitude of the location
 * @param lon - Longitude of the location
 * @param date - Date in YYYY-MM-DD format
 * @param time - Time in HH:MM format (24-hour)
 * @returns Promise resolving to WeatherResult containing temperature, windspeed, rainfall, description, icon, image URL, and link to detailed forecast
 */
export async function fetchWeather(
   lat: number,
   lon: number,
   date: string,
   time: string
): Promise<WeatherResult> {
   const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&hourly=temperature_2m,weathercode,windspeed_10m,precipitation&timezone=auto`;
   const res = await fetch(url);
   const data = await res.json();
   const target = new Date(`${date}T${time}`);
   let minDiff = Infinity;
   let idx = -1;
   for (let i = 0; i < data.hourly.time.length; i++) {
      const forecastTime = new Date(data.hourly.time[i]);
      const diff = Math.abs(forecastTime.getTime() - target.getTime());
      if (diff < minDiff) {
         minDiff = diff;
         idx = i;
      }
   }
   const temp = idx !== -1 ? data.hourly.temperature_2m[idx] : null;
   const wind = idx !== -1 ? data.hourly.windspeed_10m[idx] : null;
   const rain = idx !== -1 ? data.hourly.precipitation[idx] : null;
   const code = idx !== -1 ? data.hourly.weathercode[idx] : 0;
   const w = weatherMap[code as number] || {
      desc: "Unknown",
      icon: "fa-question",
      img: "/assets/weather/unknown.jpg",
   };
   return {
      temperature: temp,
      windspeed: wind,
      rainfall: rain,
      description: w.desc,
      icon: w.icon,
      img: w.img,
      url: `https://open-meteo.com/en/weather/${lat},${lon}`,
   };
}
