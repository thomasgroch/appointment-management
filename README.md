# Appointment Management

This is a project developed for IT's defense using Laravel Framework.

![Laravel Logo](https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)

## Setup Instructions

To setup this project, you need to follow these instructions:

### Setup

```bash
# Clone the repository
git clone https://github.com/thomasgroch/appointment-management

# Navigate to the project directory
cd appointment-management

# Install dependencies
composer install
npm install

# Setup environment file
cp .env.example .env
# Fill in database information in the .env file
```
## Run Instructions

### Generate Key 

Generate application key

```bash
php artisan key:generate
```

### Run Migrations and Seed the database

```bash
php artisan migrate:fresh --seed
```

### Run the Application

Finally, start the application:

```bash
php artisan serve
npm run dev
```

Open a web browser and navigate to the URL `http://localhost:8000`

## License

This Laravel project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
