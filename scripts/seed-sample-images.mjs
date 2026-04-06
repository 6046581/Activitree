import { mkdir, readdir, rm, writeFile } from "node:fs/promises";
import path from "node:path";

const ROOT_DIR = process.cwd();
const AVATAR_DIR = path.join(ROOT_DIR, "public", "api", "uploads", "avatars");
const ACTIVITY_DIR = path.join(ROOT_DIR, "public", "api", "uploads", "activity_photos");

const USER_COUNT = 25;
const ACTIVITY_COUNT = 36;

async function ensureDir(dirPath) {
   await mkdir(dirPath, { recursive: true });
}

async function clearGeneratedFiles(dirPath, prefix) {
   const entries = await readdir(dirPath, { withFileTypes: true }).catch(() => []);

   const removals = entries
      .filter((entry) => entry.isFile() && entry.name.startsWith(prefix))
      .map((entry) => rm(path.join(dirPath, entry.name), { force: true }));

   await Promise.all(removals);
}

async function downloadBinary(url, retries = 2) {
   let lastError = null;

   for (let attempt = 0; attempt <= retries; attempt += 1) {
      try {
         const response = await fetch(url, {
            headers: {
               "User-Agent": "activitree-sample-image-seeder/1.0",
               Accept: "image/*",
            },
         });

         if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
         }

         const arrayBuffer = await response.arrayBuffer();
         return Buffer.from(arrayBuffer);
      } catch (error) {
         lastError = error;
      }
   }

   throw lastError ?? new Error(`Failed to download ${url}`);
}

async function downloadJson(url, retries = 2) {
   let lastError = null;

   for (let attempt = 0; attempt <= retries; attempt += 1) {
      try {
         const response = await fetch(url, {
            headers: {
               "User-Agent": "activitree-sample-image-seeder/1.0",
               Accept: "application/json",
            },
         });

         if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
         }

         return await response.json();
      } catch (error) {
         lastError = error;
      }
   }

   throw lastError ?? new Error(`Failed to fetch JSON from ${url}`);
}

async function seedAvatars() {
   await ensureDir(AVATAR_DIR);
   await clearGeneratedFiles(AVATAR_DIR, "sample_");

   const profileResponse = await downloadJson(`https://randomuser.me/api/?results=${USER_COUNT}&seed=activitree-users&nat=us,gb,ca,au,nl`);
   const profiles = Array.isArray(profileResponse?.results) ? profileResponse.results : [];

   if (profiles.length < USER_COUNT) {
      throw new Error(`RandomUser returned ${profiles.length} profiles, expected ${USER_COUNT}`);
   }

   for (let id = 1; id <= USER_COUNT; id += 1) {
      const fileName = `sample_${id}.jpg`;
      const filePath = path.join(AVATAR_DIR, fileName);
      const pictureUrl = profiles[id - 1]?.picture?.large;
      if (typeof pictureUrl !== "string" || pictureUrl.length === 0) {
         throw new Error(`Missing avatar URL for profile #${id}`);
      }

      const bytes = await downloadBinary(pictureUrl);
      await writeFile(filePath, bytes);
      process.stdout.write(`Saved ${path.relative(ROOT_DIR, filePath)}\n`);
   }
}

function activitySeedTerm(activityId) {
   const keywords = [
      "hiking",
      "cooking",
      "boardgame",
      "beach",
      "city",
      "friends",
      "museum",
      "yoga",
      "photography",
      "coffee",
      "sports",
      "cycling",
      "bookclub",
      "music",
      "nature",
   ];

   return keywords[(activityId - 1) % keywords.length];
}

async function seedActivityPhotos() {
   await ensureDir(ACTIVITY_DIR);
   await clearGeneratedFiles(ACTIVITY_DIR, "sample_");

   for (let id = 1; id <= ACTIVITY_COUNT; id += 1) {
      const fileName = `sample_${id}.jpg`;
      const filePath = path.join(ACTIVITY_DIR, fileName);
      const seed = activitySeedTerm(id);
      const url = `https://picsum.photos/seed/activitree-${seed}-${id}/1280/720.jpg`;

      const bytes = await downloadBinary(url);
      await writeFile(filePath, bytes);
      process.stdout.write(`Saved ${path.relative(ROOT_DIR, filePath)}\n`);
   }
}

async function main() {
   await seedAvatars();
   await seedActivityPhotos();
   process.stdout.write("Sample image seeding complete.\n");
}

main().catch((error) => {
   console.error("Failed to seed sample images:", error);
   process.exitCode = 1;
});
