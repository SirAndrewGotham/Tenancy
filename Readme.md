## Tenancy

Multitenancy project built with Laravel 12, Livewire, Alpine and Tailwind using single database.

## Features

- Tenants (groups of users) can only work with whatever designated to those tenants: see, edit, etc. everything within a specific tenant.
- System administrator can login to a user tenant account from the SysAdmin interface temporarily to see/solve user's issues.
- To be continued...

## Installation

In your terminal cd to the project folder, then:

- php artisan key:generate
- composer install
- npm install && npm run dev (or "npm run build" if you prefer)
- Edit .env to reflect your settings and/or swap default SQLite database to something else.
- php artisan migrate --seed (will create db if none is found)
