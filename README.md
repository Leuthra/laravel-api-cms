# Laravel API CMS

![License](https://img.shields.io/github/license/Leuthra/laravel-api-cms)
![PHP Version](https://img.shields.io/badge/php-8.2-777bb4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)


A robust, headless Content Management System built with Laravel 12. Designed for flexibility, performance, and scalability, this project serves as a solid backend for modern frontend applications (Next.js, Vue, React, etc.).

## Table of Contents

- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Configuration](#configuration)
- [API Documentation](#api-documentation)
- [License](#license)

## Features

- **Headless Architecture**: pure API-first design.
- **Content Management**: Complete CRUD for Posts with support for drafts, publishing, and archiving.
- **Taxonomies**: Flexible categorization with hierarchical Categories and Tags.
- **Media Management**: Integrated media library for handling image uploads and optional manipulations.
- **Role-Based Access Control (RBAC)**: secure endpoints with Admin and Editor roles.
- **Webhooks System**: Event-driven architecture triggering external notifications on Post creation, updates, and deletion.
- **Settings Management**: Key-value settings store with cached retrieval for high performance.
- **Authentication**: Secure API authentication using Laravel Sanctum.

## Technology Stack

- **Framework**: Laravel 12
- **Language**: PHP 8.2
- **Database**: MySQL / PostgreSQL / SQLite
- **Core Packages**:
    - `spatie/laravel-permission`: Roles and Permissions.
    - `spatie/laravel-medialibrary`: File handling.
    - `spatie/laravel-activitylog`: Audit logging.
    - `kalnoy/nestedset`: Hierarchical data for categories.
    - `artesaos/seotools`: SEO helper tools.

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Database Server (MySQL, PostgreSQL, etc.)

### Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Leuthra/laravel-api-cms.git
   cd laravel-api-cms
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Environment Setup**
   Copy the example environment file and configure your database credentials.
   ```bash
   cp .env.example .env
   ```

4. **Generate App Key**
   ```bash
   php artisan key:generate
   ```

5. **Migrate and Seed**
   Run database migrations to create the schema and seed default roles/users.
   ```bash
   php artisan migrate --seed
   ```

## Configuration

### Roles and Permissions

The system comes with default roles and a super-admin bypass:
- **Super Admin**: Bypasses all checks (defined in `AppServiceProvider`).
- **Admin**: Full access to all resources and user management.
- **Editor**: Access to manage posts and taxonomies.
- **Reader**: Read-only access (can view posts).

### Webhooks

Webhooks can be configured to notify external services about content changes.
- **Events Supported**: `post.created`, `post.updated`, `post.deleted`, `post.restored`, `post.forceDeleted`.
- **Payload**: Standardized JSON payload containing the event name and the post object.

## API Documentation

The API follows RESTful standards. All responses are returned in JSON format.

### Authentication

- **Base URL**: `/api/v1`
- **Headers**: `Accept: application/json`

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| POST | `/api/v1/login` | Authenticate user and retrieve token. |
| POST | `/api/v1/logout` | Revoke current token. |

### Posts

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/posts` | List all posts (paginated, filterable). |
| POST | `/api/v1/posts` | Create a new post. |
| GET | `/api/v1/posts/{id}` | Retrieve a specific post. |
| PUT | `/api/v1/posts/{id}` | Update a post. |
| DELETE | `/api/v1/posts/{id}` | Move post to trash. |

### Taxonomies

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/taxonomies/tree` | Get hierarchical tree of categories. |
| GET | `/api/v1/taxonomies` | List all tags/categories. |
| POST | `/api/v1/taxonomies` | Create a new taxonomy term. |

### Settings

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/settings?group={name}` | Retrieve settings (cached). |
| POST | `/api/v1/settings` | Update settings (Admin only). |

## License

This project is open-source software licensed under the [MIT license](LICENSE).
