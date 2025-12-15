***

# Corporate Digital University System (NTSPI)

A comprehensive web platform designed for the **Nizhny Tagil State Social Pedagogical Institute (NTSPI)**. This system serves as a full-featured replacement for the legacy website, providing tools for content management, educational program administration, admission campaigns, and institutional structure management.

## 🚀 Tech Stack

### Frontend
*   **Framework:** Vue.js 3 (Composition API)
*   **Adapter:** Inertia.js (Monolith structure with SPA feel)
*   **Styling:** Tailwind CSS
*   **Build Tool:** Vite

### Backend
*   **Core:** PHP 8.2 / Laravel
*   **Admin Panel:** Filament PHP (CMS & Resource Management)
*   **Database:** MySQL 8.0
*   **Caching & Queues:** Redis
*   **Server:** Nginx + PHP-FPM

### Infrastructure
*   **Containerization:** Docker & Docker Compose
*   **SSL:** Certbot (Let's Encrypt)

---

## 🏗 Architecture

The backend follows the **Porto Architecture Pattern**, organizing code into logical modular units to ensure scalability and maintainability.

*   **Ship Layer:** Low-level code, parent classes, and configuration.
*   **Containers Layer:** Encapsulates business logic into modular containers (e.g., `AdditionalEducation`, `Article`, `Event`, `User`, `Schedule`, etc.).
*   **Request Lifecycle:** Route -> Controller -> Action -> Task -> UI.

---

## ✨ Key Features

*   **Content Management (CMS):** Advanced management of News, Articles, and Events with media galleries and status workflows (Draft, Published, Archived).
*   **Institute Structure:** Hierarchical management of Faculties, Departments (Kafedras), Divisions, and Staff profiles.
*   **Educational Programs:** Catalog of study programs, admission plans, and entrance exam requirements.
*   **Admissions Management:** Tools to manage "Admission Campaigns" (Priemnaya Kampaniya), enabling dynamic updates to student intake numbers and rules.
*   **Role-Based Access Control (RBAC):** Powered by **Filament Shield**. Granular permissions allowing different departments (Deans, Press Service, HR) to manage their specific sections independently.
*   **Dynamic Routing:** Custom pages and navigation structures managed directly via the Database.
*   **Cross-Model Search:** Unified global search across News, Pages, Events, and Programs using standard Eloquent models.

---

## 🛠 Installation & Local Development

The project includes a Docker environment for easy deployment.

### 1. Clone the Repository
```bash
git clone https://github.com/F4ilji/ntspi-app.git
cd ntspi-app
```

### 2. Configure Environment
Create the environment file from the example.
```bash
cp example.env .env
```

### 3. Create Directories & Set Permissions
Ensure Laravel storage directories exist and have write permissions.
```bash
mkdir -p storage/{app,logs,framework/{cache,sessions,views}}
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage
```

### 4. Start Docker Containers
Launch Nginx, MySQL, Redis, and the App container.
```bash
docker compose up --build -d
```

### 5. Install Dependencies
Enter the PHP container to run setup commands:
```bash
docker exec -it ntspi-php bash
```

Inside the container, run:
```bash
# Backend dependencies
composer install

# Database migrations
php artisan migrate

# Frontend dependencies
npm i

# Build assets
npm run build
```

### 6. Initialize System Data
Set up user roles and seed the database with initial data.
```bash
# Initialize permissions/roles (Critical for Filament Shield)
php artisan roles:init-roles

# Seed dummy data (Optional)
php artisan db:seed
```

---

## 📂 Project Structure Overview

*   `_docker/`: Docker configuration files (Nginx, PHP, MySQL).
*   `app/Containers/`: Business logic modules (Porto pattern).
*   `resources/js/`: Vue.js components, Pages, and shared UI assets.
*   `routes/`: Web and API routes (including dynamic DB-based routing logic).
