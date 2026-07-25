<?php

use Illuminate\Support\Str;
use Pdo\Mysql;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'sqlite'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
            'transaction_mode' => 'DEFERRED',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                Mysql::ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mariadb' => [
            'driver' => 'mariadb',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                Mysql::ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => env('DB_SSLMODE', 'prefer'),
        ],

        'modules' => [
            'driver' => env('MODULE_DB_CONNECTION') ?: env('DB_CONNECTION', 'pgsql'),
            'url' => env('MODULE_DB_URL') ?: env('HR_DB_URL') ?: null,
            'host' => env('MODULE_DB_HOST') ?: env('DB_HOST', '127.0.0.1'),
            'port' => env('MODULE_DB_PORT') ?: env('DB_PORT', '5432'),
            'database' => env('MODULE_DB_DATABASE') ?: env('DB_DATABASE', 'laravel'),
            'username' => env('MODULE_DB_USERNAME') ?: env('DB_USERNAME', 'root'),
            'password' => env('MODULE_DB_PASSWORD') ?: env('DB_PASSWORD', ''),
            'charset' => env('MODULE_DB_CHARSET') ?: env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('MODULE_DB_SEARCH_PATH') ?: 'public',
            'sslmode' => env('MODULE_DB_SSLMODE') ?: env('DB_SSLMODE', 'prefer'),
        ],

        'hr' => [
            // HR owns employee data. Never fall back to DB_* or MODULE_DB_*
            // here: that would silently write HR records into ITSM.
            'driver' => env('HR_DB_CONNECTION', 'pgsql'),
            'url' => env('HR_DB_URL'),
            'host' => env('HR_DB_HOST'),
            'port' => env('HR_DB_PORT', '5432'),
            'database' => env('HR_DB_DATABASE'),
            'username' => env('HR_DB_USERNAME'),
            'password' => env('HR_DB_PASSWORD'),
            'charset' => env('HR_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('HR_DB_SEARCH_PATH', 'public'),
            'sslmode' => env('HR_DB_SSLMODE', 'prefer'),
        ],

        // Inventory owns stock, warehouse, and movement data. It must never
        // fall back to the ITSM connection.
        'inventory' => [
            'driver' => env('INVENTORY_DB_CONNECTION', 'pgsql'),
            'url' => env('INVENTORY_DB_URL'),
            'host' => env('INVENTORY_DB_HOST'),
            'port' => env('INVENTORY_DB_PORT', '5432'),
            'database' => env('INVENTORY_DB_DATABASE'),
            'username' => env('INVENTORY_DB_USERNAME'),
            'password' => env('INVENTORY_DB_PASSWORD'),
            'charset' => env('INVENTORY_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('INVENTORY_DB_SEARCH_PATH', 'public'),
            'sslmode' => env('INVENTORY_DB_SSLMODE', 'prefer'),
        ],

        // Procurement owns suppliers, requisitions, purchase orders, and
        // delivery records. It must never fall back to the ITSM database.
        'procurement' => [
            'driver' => env('PROCUREMENT_DB_CONNECTION', 'pgsql'),
            'url' => env('PROCUREMENT_DB_URL'),
            'host' => env('PROCUREMENT_DB_HOST'),
            'port' => env('PROCUREMENT_DB_PORT', '5432'),
            'database' => env('PROCUREMENT_DB_DATABASE'),
            'username' => env('PROCUREMENT_DB_USERNAME'),
            'password' => env('PROCUREMENT_DB_PASSWORD'),
            'charset' => env('PROCUREMENT_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('PROCUREMENT_DB_SEARCH_PATH', 'public'),
            'sslmode' => env('PROCUREMENT_DB_SSLMODE', 'prefer'),
            // Neon/PgBouncer can reuse a server-side prepared statement after
            // a schema change, producing "cached plan must not change result
            // type". Emulated prepares keep this tenant database compatible
            // with the pooled Neon endpoint.
            'options' => extension_loaded('pdo_pgsql') ? array_filter([
                PDO::ATTR_EMULATE_PREPARES => true,
            ]) : [],
        ],
        'order_fulfillment' => [
            'driver' => env('ORDER_FULFILLMENT_DB_CONNECTION', 'pgsql'),
            'url' => env('ORDER_FULFILLMENT_DB_URL'),
            'host' => env('ORDER_FULFILLMENT_DB_HOST'),
            'port' => env('ORDER_FULFILLMENT_DB_PORT', '5432'),
            'database' => env('ORDER_FULFILLMENT_DB_DATABASE'),
            'username' => env('ORDER_FULFILLMENT_DB_USERNAME'),
            'password' => env('ORDER_FULFILLMENT_DB_PASSWORD'),
            'charset' => env('ORDER_FULFILLMENT_DB_CHARSET', 'utf8'),
            'prefix' => '', 'prefix_indexes' => true,
            'search_path' => env('ORDER_FULFILLMENT_DB_SEARCH_PATH', 'public'),
            'sslmode' => env('ORDER_FULFILLMENT_DB_SSLMODE', 'prefer'),
        ],
        'ecommerce' => [
            'driver' => env('ECOMMERCE_DB_CONNECTION', 'pgsql'),
            'url' => env('ECOMMERCE_DB_URL'),
            'host' => env('ECOMMERCE_DB_HOST'),
            'port' => env('ECOMMERCE_DB_PORT', '5432'),
            'database' => env('ECOMMERCE_DB_DATABASE'),
            'username' => env('ECOMMERCE_DB_USERNAME'),
            'password' => env('ECOMMERCE_DB_PASSWORD'),
            'charset' => env('ECOMMERCE_DB_CHARSET', 'utf8'),
            'prefix' => '', 'prefix_indexes' => true,
            'search_path' => env('ECOMMERCE_DB_SEARCH_PATH', 'public'),
            'sslmode' => env('ECOMMERCE_DB_SSLMODE', 'prefer'),
        ],
        'manufacturing' => [
            'driver' => env('MANUFACTURING_DB_CONNECTION', 'pgsql'),
            'url' => env('MANUFACTURING_DB_URL'),
            'host' => env('MANUFACTURING_DB_HOST'),
            'port' => env('MANUFACTURING_DB_PORT', '5432'),
            'database' => env('MANUFACTURING_DB_DATABASE'),
            'username' => env('MANUFACTURING_DB_USERNAME'),
            'password' => env('MANUFACTURING_DB_PASSWORD'),
            'charset' => env('MANUFACTURING_DB_CHARSET', 'utf8'),
            'prefix' => '', 'prefix_indexes' => true,
            'search_path' => env('MANUFACTURING_DB_SEARCH_PATH', 'public'),
            'sslmode' => env('MANUFACTURING_DB_SSLMODE', 'prefer'),
        ],
        'finance' => [
            // Finance owns accounting data. Never fall back to DB_* or a
            // shared module connection: this keeps financial records out of ITSM.
            'driver' => env('FINANCE_DB_CONNECTION', 'pgsql'),
            'url' => env('FINANCE_DB_URL'),
            'host' => env('FINANCE_DB_HOST'),
            'port' => env('FINANCE_DB_PORT', '5432'),
            'database' => env('FINANCE_DB_DATABASE'),
            'username' => env('FINANCE_DB_USERNAME'),
            'password' => env('FINANCE_DB_PASSWORD'),
            'charset' => env('FINANCE_DB_CHARSET', 'utf8'),
            'prefix' => '', 'prefix_indexes' => true,
            'search_path' => env('FINANCE_DB_SEARCH_PATH', 'public'),
            'sslmode' => env('FINANCE_DB_SSLMODE', 'prefer'),
        ],

        // Business Intelligence owns only analytics snapshots, audit data,
        // and future AI reports. It must never fall back to ITSM's database.
        'business_intelligence' => [
            'driver' => env('BUSINESS_INTELLIGENCE_DB_CONNECTION', 'pgsql'),
            'url' => env('BUSINESS_INTELLIGENCE_DB_URL'),
            'host' => env('BUSINESS_INTELLIGENCE_DB_HOST'),
            'port' => env('BUSINESS_INTELLIGENCE_DB_PORT', '5432'),
            'database' => env('BUSINESS_INTELLIGENCE_DB_DATABASE'),
            'username' => env('BUSINESS_INTELLIGENCE_DB_USERNAME'),
            'password' => env('BUSINESS_INTELLIGENCE_DB_PASSWORD'),
            'charset' => env('BUSINESS_INTELLIGENCE_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('BUSINESS_INTELLIGENCE_DB_SEARCH_PATH', 'public'),
            'sslmode' => env('BUSINESS_INTELLIGENCE_DB_SSLMODE', 'prefer'),
        ],

        // Staging is optional. It must be configured explicitly: falling back
        // to DB_* would silently put integration data into the ITSM database.
        'staging' => [
            'driver' => env('STAGING_DB_CONNECTION', 'pgsql'),
            'url' => env('STAGING_DB_URL') ?: null,
            'host' => env('STAGING_DB_HOST'),
            'port' => env('STAGING_DB_PORT'),
            'database' => env('STAGING_DB_DATABASE'),
            'username' => env('STAGING_DB_USERNAME'),
            'password' => env('STAGING_DB_PASSWORD'),
            'charset' => env('STAGING_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('STAGING_DB_SEARCH_PATH', 'public'),
            'sslmode' => env('STAGING_DB_SSLMODE', 'prefer'),
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-database-'),
            'persistent' => env('REDIS_PERSISTENT', false),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

    ],

];
