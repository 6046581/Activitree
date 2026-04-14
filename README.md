# Activitree

This is a web application initially built for a school project. The actual project requirements were pretty basic, but we wanted to go further with it by making use of an experimental new frontend framework and creating a backend API similar to how is usually done in real fullstack applications.

Activitree is an event planning application built with Ripple, Vite, TypeScript, and a PHP/MySQL backend. It lets users create and manage activities, invite participants, view locations, and handle authentication from the browser.

> Note: not actually meant to be used anywhere, RippleTS is not production ready and this project was just to show off our skills.

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

- Bun 1.3 or newer
- Docker Desktop

## Development Setup

### Start

```bash
docker compose up --build
```

### Services

- Frontend: `http://localhost:3000`
- API: `http://localhost:8080/api`
- MySQL: `localhost:3307`

## Dev Commands

- `bun install` - install dependencies
- `bun run dev` - start the Vite development server
- `bun run build` - build the production bundle
- `bun run serve` - preview the production build locally
- `bun run lint` - run ESLint
- `bun run format` - format the codebase with Prettier
- `bun run format:check` - verify formatting without writing changes

## Environment Variables

The Vite config supports these optional variables:

- `VITE_BASE_URL` - base path for the app, defaults to `/`
- `VITE_DEV_API_TARGET` - PHP server target for local development, defaults to `http://localhost`
- `VITE_DEV_API_BASE_PATH` - backend path used by the dev proxy, defaults to `/activitree/public/api`

## Backend API

The PHP backend exposes routes for users, activities, and locations. The main entrypoint is `public/api/index.php`, which wires requests into the controllers under `public/api/classes/`.
More API information can be found in `documentation/api.md`.
