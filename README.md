# Activitree

This is an application built for a school project. somthing something something, but we wanted to go further with it by making use of an experimental new frontend framework and creating a backend API similar to how is usually done in real fullstack applications.
Activitree is an event planning application built with Ripple, Vite, TypeScript, and a PHP/MySQL backend. It lets users create and manage activities, invite participants, view locations, and handle authentication from the browser.

## Features

- User registration and login
- Activity creation, editing, deletion, and participation management
- Activity details like weather and location using API's
- Role-based access for regular users and administrators

## Tech Stack

- Frontend: Ripple, Ripple Router, TypeScript, Vite, Notyf
- Backend: PHP
- Database: MySQL
- Utilities: Bun, ESLint, Prettier

## Project Structure

- `src/` - frontend app, pages, components, and client helpers
- `public/api/` - PHP API entrypoint, router, controllers, and helpers
- `documentation/` - planning notes, diagrams, SQL schema, and product docs
- `documentation/db.sql` - database schema for the database, along with mock data for testing

## Requirements

- Node.js 20 or newer
- Bun 1.3 or newer
- PHP 8+ with a web server
- MySQL or MariaDB

## Setup

### 1. Install dependencies

```bash
bun install # or npm/pnpm/yarn
```

### 2. Import the database

Create a MySQL database named `activitree`, then import `documentation/db.sql`.

### 3. Run the PHP API

Serve the `public/api` directory through your PHP-capable web/dev server so `index.php` is reachable. The frontend expects the API to be available under `/api`.

### 4. Start the frontend

```bash
bun run dev
```

The Vite dev server runs on port `3000`.

## Environment Variables

The Vite config supports these optional variables:

- `VITE_BASE_URL` - base path for the app, defaults to `/`
- `VITE_DEV_API_TARGET` - PHP server target for local development, defaults to `http://localhost`
- `VITE_DEV_API_BASE_PATH` - backend path used by the dev proxy, defaults to `/activitree/public/api`

## Scripts

- `bun run dev` - start the Vite development server
- `bun run build` - build the production bundle
- `bun run serve` - preview the production build locally
- `bun run lint` - run ESLint
- `bun run format` - format the codebase with Prettier
- `bun run format:check` - verify formatting without writing changes

## API Overview

The PHP backend exposes routes for users, activities, and locations. The main entrypoint is `public/api/index.php`, which wires requests into the controllers under `public/api/classes/`.
Additional information can be found in `documentation/api.md`.
