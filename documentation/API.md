# Activitree API Documentation

## Overview

Activitree exposes a PHP JSON API from `public/api/index.php` for authentication, users, activities, and locations. Responses are JSON, with success payloads typically wrapped in `data` or `ok`, and errors returned as `{ "error": "..." }` with an HTTP status code.

## Base URL

- Frontend development proxy: `/api`
- Typical local backend path: `http://localhost/activitree/public/api`

## Authentication

Protected endpoints expect a bearer token:

```http
Authorization: Bearer <token>
```

Tokens are issued on login and stored in `users.token`. Missing or invalid tokens return `401`; insufficient role access returns `403`.

## Shared Rules

- Public routes require no token.
- List endpoints support `limit` and `offset` query parameters.
- Defaults: users and activities use `100`, locations use `200`.
- Passwords are hashed with `password_hash`.

## Routes

### Users

### `POST /users/login`

Log in with `{ "email": "...", "password": "..." }`.

- Success: `200` with `{ "user": ..., "token": "..." }`
- Errors: `400` if fields are missing, `401` if credentials are invalid

### `POST /users/logout`

Clear the current user's token.

- Auth: required
- Success: `200` with `{ "ok": true }`

### `POST /users/signup`

Create a new account with `{ "username": "...", "email": "...", "password": "..." }`.

- Success: `201` with `{ "id": <newId> }`
- Errors: `400` if required fields are missing, `500` if creation fails

### `GET /users`

List users.

- Auth: admin only
- Response: `200` with `{ "data": [ ... ] }`

Returned fields: `id`, `username`, `email`, `role`, `created_at`

Also includes: `profile_picture_path`, `profile_picture_url`

### `GET /users/{id}`

Return one user.

- Auth: required
- Access: admin or the user themself
- Response: `200` with `{ "data": { ... } }`

Returned fields: `id`, `username`, `email`, `role`

Also includes: `profile_picture_path`, `profile_picture_url`

### `PUT /users/{id}`

Update `username` and/or `email`.

- Auth: required
- Access: admin or the user themself
- Missing fields keep their current value
- Success: `200` with `{ "ok": true }`

### `DELETE /users/{id}`

Delete a user account.

- Auth: required
- Access: admin or the user themself
- Success: `200` with `{ "ok": true }`

### `POST /users/{id}/profile-picture`

Upload or replace a profile picture for a user.

- Auth: required
- Access: admin or the user themself
- Content type: `multipart/form-data`
- File field name: `file` (or `profile_picture`)
- Allowed image types: JPG, PNG, WEBP, GIF
- Max file size: 8 MB
- Success: `200` with `{ "ok": true, "profile_picture_path": "uploads/profile_pictures/...", "profile_picture_url": "..." }`

### Activities

### `GET /activities`

List activities ordered by `activity_time`.

- Response: `200` with `{ "data": [ ... ] }`

Returned fields: `id`, `title`, `description`, `activity_type`, `status`, `activity_time`, `location_id`, `created_by`, `created_by_username`, `participant_ids`

Also includes: `photo_path`, `photo_url`

### `GET /activities/{id}`

Return one activity.

- Response: `200` with `{ "data": { ... } }`
- Errors: `400` for an invalid id, `404` if missing

### `POST /activities`

Create an activity.

- Auth: required
- Required body fields: `title`, `activity_time`
- Optional fields: `description`, `activity_type` (defaults to `indoor`), `status` (defaults to `planned`), `location_id`
- Success: `201` with `{ "id": <newId> }`

### `GET /activities/{id}/participants`

List participants for an activity.

- Response: `200` with `{ "data": [ ... ] }`

Returned fields: `id`, `username`, `email`, `role`, `activity_role`, `joined_at`

### `POST /activities/{id}/join`

Join an activity as the current user.

- Auth: required
- Success: `200` with `{ "ok": true }`
- Errors: `404` if the activity does not exist, `409` if already joined

### `DELETE /activities/{id}/leave`

Leave an activity.

- Auth: required
- Success: `200` with `{ "ok": true }`
- Errors: `404` if the activity does not exist or the user is not participating

### `PUT /activities/{id}`

Update an activity.

- Auth: required
- Access: admin or the activity creator
- All fields are optional and fall back to existing values
- Success: `200` with `{ "ok": true }`

### `DELETE /activities/{id}`

Delete an activity.

- Auth: required
- Access: admin or the activity creator
- Success: `200` with `{ "ok": true }`
- Failure: `404` if the activity does not exist or the user is not allowed to delete it

### `POST /activities/{id}/photo`

Upload or replace a photo for an activity.

- Auth: required
- Access: admin or the activity creator
- Content type: `multipart/form-data`
- File field name: `file` (or `photo`)
- Allowed image types: JPG, PNG, WEBP, GIF
- Max file size: 8 MB
- Success: `200` with `{ "ok": true, "photo_path": "uploads/activity_photos/...", "photo_url": "..." }`

### Locations

### `GET /locations`

List locations.

- Response: `200` with `{ "data": [ ... ] }`

Returned fields: `id`, `latitude`, `longitude`, `country`, `country_code`, `city`, `postal_code`, `street`, `house_number`, `formatted_address`

### `GET /locations/{id}`

Return one location.

- Response: `200` with `{ "data": { ... } }`
- Errors: `400` for an invalid id, `404` if missing

## Data Model

Core tables: `users`, `activities`, `locations`, `activity_participants`, `invitations`, and `notifications`.

## Implementation Notes

- `public/api/index.php` is the route entrypoint.
- `public/api/router.php` handles method/path matching.
- The frontend sends auth as a bearer token.
- The controller includes an `updateUserPassword` method, but it is not routed in the current API map.
