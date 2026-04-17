<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
A photo gallery application built with Laravel, Inertia.js, and Vue 3.
</p>

## Overview

This is a photography portfolio/gallery application that allows you to:

- **Organize photos into albums** with titles, descriptions, and tags
- **Manage albums** through an admin panel (create, edit, publish, delete)
- **Upload images** with automatic watermarking and thumbnail generation
- **Display albums** on a public frontend filtered by tags

## Tech Stack

- **Backend**: Laravel 13 (PHP 8.3+)
- **Frontend**: Vue 3 with Inertia.js
- **Styling**: Tailwind CSS
- **Image Processing**: Intervention Image
- **Database**: MySQL (default) or SQLite for local development
- **Containerization**: Docker with Traefik reverse proxy

## Project Structure

```
app/
├── Http/Controllers/
│   ├── Admin/           # Admin panel controllers (AlbumController, ImageController, TagController, AuthController)
│   └── Frontend/        # Public-facing controllers (AlbumController, HomeController)
├── Models/              # Eloquent models (Album, Image, Tag, User)
├── Providers/           # Service providers
└── Services/           # Business logic (AlbumService, ImageService, TagService)

resources/js/
├── Components/         # Reusable Vue components
├── Layouts/            # Page layouts
└── Pages/              # Inertia pages (Home, Album, Admin/*)

database/migrations/     # Database schema
routes/web.php          # Route definitions
config/photos.php       # Photo gallery settings
```

## Data Models

### Album
- `title` - Album name
- `slug` - URL-friendly identifier
- `description` - Optional description
- `is_published` - Whether the album is publicly visible
- `published_at` - Publication timestamp
- `cover_image_id` - Reference to the cover image

### Image
- `album_id` - Parent album
- `watermark_path` - Path to watermarked full-size image
- `thumbnail_path` - Path to generated thumbnail
- `width` / `height` - Original image dimensions
- `position` - Sort order within album

### Tag
- `name` - Tag display name
- `slug` - URL-friendly identifier

## Setup

### Using Docker (Recommended)

1. Create the Docker network:
   ```bash
   docker network create photos
   ```

2. Start the containers:
   ```bash
   docker compose up -d
   ```

3. Install dependencies and set up the application:
   ```bash
   docker compose exec php composer install
   docker compose exec php php artisan key:generate
   docker compose exec php php artisan migrate
   docker compose exec js npm install
   docker compose exec js npm run build
   ```

4. Access the application at `http://photos`

### Local Development (Without Docker)

1. Install dependencies:
   ```bash
   composer install
   npm install
   ```

2. Copy and configure environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Set up SQLite database (or configure MySQL in `.env`):
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

4. Build assets and start dev server:
   ```bash
   npm run build
   php artisan serve
   ```

5. For live reload during development:
   ```bash
   composer run dev
   ```

## Configuration

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_NAME` | Application name (used in watermarks) | `Laravel` |
| `PHOTOS_THUMBNAIL_MAX_DIMENSION` | Max thumbnail dimension in pixels | `800` |

### Photo Settings

Edit `config/photos.php` or set environment variables:

```php
'thumbnail_max_dimension' => env('PHOTOS_THUMBNAIL_MAX_DIMENSION', 800),
```

### Watermark Configuration

Watermark settings are in `app/Services/ImageService.php`:

- `watermark_font_size_ratio` - Font size relative to image (default: 0.02)
- `watermark_opacity` - Text opacity 0-100 (default: 30)
- `watermark_spacing_ratio` - Spacing between watermarks (default: 0.30)
- `watermark_font` - Path to font file

## Image Processing

When images are uploaded:

1. **Full-size watermarked image** is generated with tiled text overlay
2. **Thumbnail** is created (default max 800px, configurable)
3. Both versions are stored in `storage/app/public/images/`

## Routes

### Public Routes
- `GET /` - Home page with published albums
- `GET /albums/{slug}` - Album gallery page

### Admin Routes
- `GET /login` - Admin login
- `POST /login` - Authenticate
- `GET /admin/albums` - Album list
- `GET /admin/albums/create` - Create album form
- `POST /admin/albums` - Store new album
- `GET /admin/albums/{id}/edit` - Edit album form
- `PUT /admin/albums/{id}` - Update album
- `DELETE /admin/albums/{id}` - Delete album
- `POST /admin/albums/{id}/images` - Upload images
- `PUT /admin/albums/{id}/images/reorder` - Reorder images
- `DELETE /admin/images/{id}` - Delete image
- `GET /admin/tags` - Tag management

## Creating the First Admin User

```bash
php artisan tinker
```

Then run:
```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('your-password'),
]);
```

## Development

### Running Tests
```bash
composer run test
```

### Code Style
```bash
./vendor/bin/pint
```

## License

MIT
