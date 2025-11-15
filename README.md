# Abandoned Arcade

A web-based retro game arcade platform built with Laravel. Discover, play, and preserve classic games from various eras. Features a dark theme with neon blue accents, guest mode support, and comprehensive game management.

## Features

- Game collection browser with genre filtering and search
- Multiple game format support (HTML5, Flash via Ruffle, embedded games)
- User authentication and profiles
- Game reviews and ratings system
- Save state management for games
- Guest mode with localstorage support
- Dark theme with neon blue styling
- Responsive design

## Tech Stack

- Backend: Laravel (PHP)
- Frontend: Bootstrap 5, Sass, JavaScript
- Database: Neon (PostgreSQL) - serverless PostgreSQL database
- Asset Bundling: Vite
- Authentication: Laravel Sanctum, WorkOS (optional)

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```
3. Copy `.env.example` to `.env` and configure:
   - Set `DB_CONNECTION=pgsql` for Neon database
   - Configure Neon connection: `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` from your Neon project
   - Set `DB_SSLMODE=require` for secure connection
4. Generate application key:
   ```bash
   php artisan key:generate
   ```
5. Run migrations:
   ```bash
   php artisan migrate
   ```
6. Build assets:
   ```bash
   npm run build
   ```
7. Start development server:
   ```bash
   php artisan serve
   ```

## Game Files

Games are stored in `public/games/` directory. Each game can be:
- A single HTML file
- A folder with multiple files (use `index.html` as entry point)
- Flash games (.swf) played via Ruffle

Add games by placing files in `public/games/` and creating database entries via tinker or seeders.

## License

MIT License - See LICENSE file for details
