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
   const hasCoordinates =
      latitude !== undefined && longitude !== undefined && latitude !== null && longitude !== null;
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
