# Multi-Tenant URL Shortener Service

A multi-tenant URL shortener service built with Laravel. This project enables companies to manage and generate short URLs with a structured roles and permissions system.

---

## Architecture & Features

- **Multi-Tenant Scoping**: All users are scoped to their respective companies.
- **Role-Based Authorization**: Supports `SuperAdmin`, `Admin`, `Member`, `Sales`, and `Manager` roles.
  - **SuperAdmin**: Managed globally, can invite new company admins, toggle between creating a new company or selecting from existing companies, and view all short URLs. Can NOT create short URLs.
  - **Admin / Manager**: Can create short URLs, see all short URLs within their company, and invite other company users.
  - **Member / Sales**: Can create short URLs but can only see short URLs created by themselves.
- **Enterprise Design Patterns**: Separated into **Services**, **Repositories**, and **Traits** for a clean, decoupled architecture.
- **Test-Driven Design**: Supported by a comprehensive Pest suite of 34 tests validating all business constraints.
- **Raw SQL Seed**: The SuperAdmin user is seeded using raw SQL queries to satisfy core specifications.
- **Plain Text Email Dispatch**: Sends raw invitation links to invitees upon creation.

---

## Live Demo Access

You can access the live deployed demo of the application here:
- **Demo URL**: [https://url-shortner.sudhirrajai.com/login](https://url-shortner.sudhirrajai.com/login)
- **SuperAdmin Email**: `superadmin@example.com`
- **SuperAdmin Password**: `password`

---

## Local Setup Instructions

Follow these steps to set up the project locally for testing:

### 1. Prerequisites
Ensure you have the following installed on your machine:
- PHP 8.2 or 8.3
- Composer
- Node.js & NPM
- SQLite (or MySQL)

### 2. Clone the Repository
```bash
git clone https://github.com/sudhirrajai/url-shortner.git
cd url-shortner
```

### 3. Install Dependencies
```bash
composer install
npm install
```

### 4. Environment Configuration
Copy the `.env.example` file to `.env`:
```bash
cp .env.example .env
```
Open `.env` and verify your database connection credentials. If you are using SQLite, configure:
```ini
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/your/database.sqlite
```
*(Otherwise configure your MySQL server credentials).*

### 5. Generate Key & Run Database Setup
Generate your application key:
```bash
php artisan key:generate
```
Run database migrations and seed the default SuperAdmin account:
```bash
php artisan migrate:fresh --seed
```
*The `DatabaseSeeder` inserts the default SuperAdmin account (`superadmin@example.com` / `password`) using raw SQL.*

### 6. Build Assets
Compile the frontend assets using Vite:
```bash
npm run build
```

### 7. Run the Application
Start the local server:
```bash
php artisan serve
```
Navigate to `http://localhost:8000` in your web browser.

---

## Running Tests
Run the Pest test suite to verify all business rules:
```bash
php artisan test --compact
```
*(All 34 tests will run and pass).*

---

## AI Usage Declaration
In compliance with the Acceptable AI Usage Policy, here are the AI tool details used in this project:
- **Used Antigravity / Inline AI assistant for**:
  - Code debugging and error understanding.
  - Quick inline code suggestions and completions in the editor.