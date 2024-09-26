
# Symfony App Boilerplate with Modular Architecture

This boilerplate provides a solid foundation to start building your Symfony applications using modular architecture, JWT authentication, PostgreSQL as a database, and Heroku for easy deployment.

## Features

- **Modular Architecture**: The application is designed following a modular approach for better scalability, maintainability, and separation of concerns.
- **JWT Authentication**: JSON Web Token (JWT) authentication is pre-configured, providing an out-of-the-box authentication system.
- **PostgreSQL Integration**: The app is pre-configured to work with PostgreSQL, offering a robust and scalable relational database.
- **Heroku Deployment**: The configuration is ready for easy deployment to Heroku with a few simple steps.

## Table of Contents

- [Getting Started](#getting-started)
- [Prerequisites](#prerequisites)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [Deployment to Heroku](#deployment-to-heroku)
- [Useful Commands](#useful-commands)
- [Contributing](#contributing)
- [License](#license)

## Getting Started

These instructions will help you set up and run the project on your local machine for development and testing purposes.

## Prerequisites

Before you begin, make sure you have the following installed:

- [PHP 8.1+](https://www.php.net/downloads.php)
- [Composer](https://getcomposer.org/)
- [PostgreSQL](https://www.postgresql.org/download/) or run via Docker: `docker-compose up -d`
- [Heroku CLI](https://devcenter.heroku.com/articles/heroku-cli)

## Project Structure

The boilerplate follows a modular architecture, where each module (feature) is isolated into its own directory. Here’s a high-level overview of the project structure:

```
.
├── src/
│   ├── Core/         # Core module
│   ├── Auth/         # JWT authentication module
│   └── [other modules]
├── config/               # Configuration files
├── migrations/           # Database migrations
├── public/               # Public directory (front controller, assets)
├── .env                  # Environment variables
├── composer.json         # Composer dependencies
└── README.md             # This file
```

## Installation

Follow these steps to get the project up and running on your local machine:

1. **Clone the repository:**

```bash
git clone https://github.com/your-repo/symfony-app-boilerplate.git
cd symfony-app-boilerplate
```

2. **Install dependencies:**

```bash
composer install
```

3. **Set up environment variables:**

Copy the `.env` file and configure it with your database credentials and other settings.

```bash
cp .env .env.local
```

Update the `.env.local` file:

```ini
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/your_database"
JWT_SECRET_KEY="%kernel.project_dir%/config/jwt/private.pem"
JWT_PUBLIC_KEY="%kernel.project_dir%/config/jwt/public.pem"
JWT_PASSPHRASE=your_jwt_passphrase
```

4. **Generate JWT keys:**

```bash
mkdir -p config/jwt
openssl genpkey -out config/jwt/private.pem -algorithm rsa -pkeyopt rsa_keygen_bits:4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

5. **Run migrations:**

```bash
php bin/console doctrine:migrations:migrate
```

## Configuration

### Database Configuration

The project is set up to use PostgreSQL. Make sure to update the `DATABASE_URL` in your `.env.local` file as needed. You can also configure the database in `config/packages/doctrine.yaml`.

### JWT Configuration

The boilerplate includes a ready-to-use JWT authentication system, managed using [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle). Make sure you have generated the JWT keys as described in the installation section.

## Running the Application

### Local Development

To start the application locally, you can use the Symfony CLI or the built-in PHP server.

```bash
php bin/console server:run
```

Alternatively, you can use:

```bash
symfony server:start
```

The application should now be accessible at `http://localhost:8000`.

## Deployment to Heroku

This boilerplate is configured to deploy easily to Heroku. Follow these steps:

1. **Create a Heroku app:**

```bash
heroku create your-app-name
```

2. **Set up PostgreSQL on Heroku:**

```bash
heroku addons:create heroku-postgresql:hobby-dev
```

Or you can set up from the Heroku dashboard. 

3. **Configure environment variables:**

Set up environment variables on Heroku for your database and JWT settings:

```bash
heroku config:set APP_SECRET=8332c1db08157d7db1154076016a434a
heroku config:set DATABASE_URL="postgresql://user:password@host:port/database"
heroku config:set JWT_SECRET_KEY="private_key_here"
heroku config:set JWT_PUBLIC_KEY="public_key_here"
heroku config:set JWT_PASSPHRASE="your_jwt_passphrase"
```

5. **Deploy to Heroku (in progress):**

There are two ways to deploy to Heroku:

- **Using Git:**

```bash
git push heroku main
```

- **Using GitHub Actions**

Push your code to GitHub and run the GitHub Actions workflow to deploy to Heroku.


6. **Run migrations:**

After deploying, run database migrations on Heroku:

```bash
heroku run php bin/console doctrine:migrations:migrate
```

Your application should now be live on Heroku!

## Useful Commands

**Running tests:**

```bash
php bin/phpunit
```

**Clearing cache:**

```bash
php bin/console cache:clear
```

**Running database migrations:**

```bash
php bin/console doctrine:migrations:migrate
```

** Linting and fixing code:**

```bash
make cs_fix
```
