# Procurement module setup

Procurement is served from the shared Nexora application at `/procurement`.
It uses the approved HR employee session and a dedicated `procurement`
database connection; it never falls back to the ITSM database.

Set these runtime values in DigitalOcean before enabling the module:

```dotenv
PROCUREMENT_DB_CONNECTION=pgsql
PROCUREMENT_DB_URL=postgresql://…
PROCUREMENT_DB_SSLMODE=require
```

Then run this **once**, with the owner role for the Procurement database:

```sh
php artisan procurement:install-schema --force
```

For an existing Procurement database whose original tables are already
present, run this one-time upgrade instead (or after the migration):

```sh
php artisan procurement:ensure-client-columns --no-interaction
```

Neither command is part of the web-process startup command. This prevents an
incorrect database role from taking the deployed application offline.
