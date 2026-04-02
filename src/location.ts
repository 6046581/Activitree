/**
 * Returns a Google Maps URL for directions or search based on location data.
 * @param location - Object with latitude, longitude, name, address
 */
export function getLocationMapUrl(
   location: {
      latitude?: number;
      longitude?: number;
      name?: string;
      address?: string;
   } = {}
): string {
   const { latitude, longitude, name, address } = location;
   const hasCoordinates = latitude !== undefined && longitude !== undefined && latitude !== null && longitude !== null;
   if (hasCoordinates) {
      return `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(`${latitude},${longitude}`)}&travelmode=driving`;
   }
   const locationLabel = [name, address].filter(Boolean).join(", ").trim();
   if (locationLabel.length > 0) {
      return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(locationLabel)}`;
   }
   return "https://www.google.com/maps";
}

/**
 * Returns a label for a location (name, address, or both).
 * @param location - Object with name, address
 */
export function getLocationLabel(location: { name?: string; address?: string } = {}): string {
   return [location.name, location.address].filter(Boolean).join(", ").trim();
}

/**
 * Returns a keyless map preview image URL for a location.
 * Primary: centered static map with marker.
 * Fallback: colorful cartographic tile.
 */
export function getLocationPreviewUrl(
   location: {
      latitude?: number;
      longitude?: number;
   } = {},
   variant: "primary" | "fallback" = "primary"
): string {
   const latitude = Number(location.latitude);
   const longitude = Number(location.longitude);

   if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
      return "/assets/placeholder.jpg";
   }

   if (variant === "primary") {
      const center = `${latitude},${longitude}`;
      // Centered static map + marker keeps the target location visually exact.
      return `https://staticmap.openstreetmap.de/staticmap.php?center=${center}&zoom=16&size=1200x600&markers=${center},red-pushpin`;
   }

   const zoom = 14;
   const safeLat = Math.min(85.05112878, Math.max(-85.05112878, latitude));
   const safeLng = ((((longitude + 180) % 360) + 360) % 360) - 180;
   const tilesPerAxis = 2 ** zoom;

   const x = Math.floor(((safeLng + 180) / 360) * tilesPerAxis);
   const latRad = (safeLat * Math.PI) / 180;
   const y = Math.floor(((1 - Math.log(Math.tan(latRad) + 1 / Math.cos(latRad)) / Math.PI) / 2) * tilesPerAxis);

   // Colorful fallback style if static map host is unavailable.
   return `https://a.basemaps.cartocdn.com/rastertiles/voyager/${zoom}/${x}/${y}@2x.png`;
}
