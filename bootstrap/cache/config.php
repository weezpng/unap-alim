<?php return array (
  'app' => 
  array (
    'name' => 'GESTÃO DE ALIMENTAÇÃO',
    'env' => 'production',
    'debug' => true,
    'url' => 'localhost',
    'asset_url' => NULL,
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => 'base64:a/82FmWHsJS+j0i7EXUf2tBal/QVUnyamV7WQQMzJlM=',
    'cipher' => 'AES-256-CBC',
    'PESSOAL_EMAIL' => 'cpess.unap.pessoal@exercito.pt',
    'APP_VERSION' => '3.01.064-RC.10',
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Cookie\\CookieServiceProvider',
      6 => 'Illuminate\\Database\\DatabaseServiceProvider',
      7 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      8 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      9 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      10 => 'Illuminate\\Hashing\\HashServiceProvider',
      11 => 'Illuminate\\Mail\\MailServiceProvider',
      12 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      13 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      14 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      15 => 'Illuminate\\Queue\\QueueServiceProvider',
      16 => 'Illuminate\\Redis\\RedisServiceProvider',
      17 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      18 => 'Illuminate\\Session\\SessionServiceProvider',
      19 => 'Illuminate\\Translation\\TranslationServiceProvider',
      20 => 'Illuminate\\Validation\\ValidationServiceProvider',
      21 => 'Illuminate\\View\\ViewServiceProvider',
      22 => 'Barryvdh\\DomPDF\\ServiceProvider',
      23 => 'SimpleSoftwareIO\\QrCode\\QrCodeServiceProvider',
      24 => 'App\\Providers\\AppServiceProvider',
      25 => 'App\\Providers\\AuthServiceProvider',
      26 => 'App\\Providers\\EventServiceProvider',
      27 => 'App\\Providers\\RouteServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Arr' => 'Illuminate\\Support\\Arr',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Http' => 'Illuminate\\Support\\Facades\\Http',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'Str' => 'Illuminate\\Support\\Str',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
      'PDF' => 'Barryvdh\\DomPDF\\Facade',
      'QrCode' => 'SimpleSoftwareIO\\QrCode\\Facades\\QrCode',
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'api' => 
      array (
        'driver' => 'token',
        'provider' => 'users',
        'hash' => false,
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Models\\User',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
        'throttle' => 60,
      ),
    ),
    'password_timeout' => 10800,
  ),
  'backup' => 
  array (
    'mysql' => 
    array (
      'mysql_path' => 'mysql',
      'mysqldump_path' => 'mysqldump',
      'compress' => true,
      'local-storage' => 
      array (
        'disk' => 'local',
        'path' => 'backups',
      ),
      'cloud-storage' => 
      array (
        'enabled' => false,
        'disk' => 's3',
        'path' => 'path/to/your/backup-folder/',
        'keep-local' => true,
      ),
    ),
  ),
  'broadcasting' => 
  array (
    'default' => 'log',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => NULL,
        'secret' => NULL,
        'app_id' => NULL,
        'options' => 
        array (
          'cluster' => NULL,
          'useTLS' => true,
        ),
      ),
      'ably' => 
      array (
        'driver' => 'ably',
        'key' => NULL,
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'cache' => 
  array (
    'default' => 'array',
    'stores' => 
    array (
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      'array' => 
      array (
        'driver' => 'array',
        'serialize' => false,
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
        'lock_connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => 'C:\\inetpub\\wwwplatforms\\_app_alim\\storage\\framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
      ),
      'dynamodb' => 
      array (
        'driver' => 'dynamodb',
        'key' => NULL,
        'secret' => NULL,
        'region' => 'us-east-1',
        'table' => 'cache',
        'endpoint' => NULL,
      ),
    ),
    'prefix' => 'gestao_de_alimentacao_cache',
  ),
  'cors' => 
  array (
    'paths' => 
    array (
      0 => 'api/*',
    ),
    'allowed_methods' => 
    array (
      0 => '*',
    ),
    'allowed_origins' => 
    array (
      0 => '*',
    ),
    'allowed_origins_patterns' => 
    array (
    ),
    'allowed_headers' => 
    array (
      0 => '*',
    ),
    'exposed_headers' => 
    array (
    ),
    'max_age' => 0,
    'supports_credentials' => false,
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'url' => NULL,
        'database' => 'cmdpessunap_alim_manager',
        'prefix' => '',
        'foreign_key_constraints' => true,
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'url' => NULL,
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'cmdpessunap_alim_manager',
        'username' => 'unaplocal',
        'password' => 'CC1@dmin18',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'url' => NULL,
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'cmdpessunap_alim_manager',
        'username' => 'unaplocal',
        'password' => 'CC1@dmin18',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'schema' => 'public',
        'sslmode' => 'prefer',
      ),
      'sqlsrv' => 
      array (
        'driver' => 'sqlsrv',
        'url' => NULL,
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'cmdpessunap_alim_manager',
        'username' => 'unaplocal',
        'password' => 'CC1@dmin18',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'client' => 'phpredis',
      'options' => 
      array (
        'cluster' => 'redis',
        'prefix' => 'gestao_de_alimentacao_database_',
      ),
      'default' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => '0',
      ),
      'cache' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => '1',
      ),
    ),
  ),
  'excel' => 
  array (
    'exports' => 
    array (
      'chunk_size' => 1000,
      'pre_calculate_formulas' => false,
      'strict_null_comparison' => false,
      'csv' => 
      array (
        'delimiter' => ',',
        'enclosure' => '"',
        'line_ending' => '
',
        'use_bom' => false,
        'include_separator_line' => false,
        'excel_compatibility' => false,
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
    ),
    'imports' => 
    array (
      'read_only' => true,
      'ignore_empty' => false,
      'heading_row' => 
      array (
        'formatter' => 'slug',
      ),
      'csv' => 
      array (
        'delimiter' => ',',
        'enclosure' => '"',
        'escape_character' => '\\',
        'contiguous' => false,
        'input_encoding' => 'UTF-8',
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
    ),
    'extension_detector' => 
    array (
      'xlsx' => 'Xlsx',
      'xlsm' => 'Xlsx',
      'xltx' => 'Xlsx',
      'xltm' => 'Xlsx',
      'xls' => 'Xls',
      'xlt' => 'Xls',
      'ods' => 'Ods',
      'ots' => 'Ods',
      'slk' => 'Slk',
      'xml' => 'Xml',
      'gnumeric' => 'Gnumeric',
      'htm' => 'Html',
      'html' => 'Html',
      'csv' => 'Csv',
      'tsv' => 'Csv',
      'pdf' => 'Dompdf',
    ),
    'value_binder' => 
    array (
      'default' => 'Maatwebsite\\Excel\\DefaultValueBinder',
    ),
    'cache' => 
    array (
      'driver' => 'memory',
      'batch' => 
      array (
        'memory_limit' => 60000,
      ),
      'illuminate' => 
      array (
        'store' => NULL,
      ),
    ),
    'transactions' => 
    array (
      'handler' => 'db',
    ),
    'temporary_files' => 
    array (
      'local_path' => 'C:\\inetpub\\wwwplatforms\\_app_alim\\storage\\framework/laravel-excel',
      'remote_disk' => NULL,
      'remote_prefix' => NULL,
      'force_resync_remote' => NULL,
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => 'C:\\inetpub\\wwwplatforms\\_app_alim\\storage\\app',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => 'C:\\inetpub\\wwwplatforms\\_app_alim\\storage\\app/public',
        'url' => 'localhost/storage',
        'visibility' => 'public',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => NULL,
        'secret' => NULL,
        'region' => NULL,
        'bucket' => NULL,
        'url' => NULL,
        'endpoint' => NULL,
      ),
      'ementa_uploads' => 
      array (
        'driver' => 'local',
        'root' => 'C:\\inetpub\\wwwplatforms\\_app_alim\\storage\\app\\public\\filesys',
      ),
    ),
    'links' => 
    array (
      'C:\\inetpub\\wwwplatforms\\_app_alim\\public\\storage' => 'C:\\inetpub\\wwwplatforms\\_app_alim\\storage\\app/public',
    ),
  ),
  'hashing' => 
  array (
    'driver' => 'bcrypt',
    'bcrypt' => 
    array (
      'rounds' => 10,
    ),
    'argon' => 
    array (
      'memory' => 1024,
      'threads' => 2,
      'time' => 2,
    ),
  ),
  'logging' => 
  array (
    'default' => 'errorlog',
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'single',
        ),
        'ignore_exceptions' => false,
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => 'C:\\inetpub\\wwwplatforms\\_app_alim\\storage\\logs/laravel.log',
        'level' => 'debug',
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => 'C:\\inetpub\\wwwplatforms\\_app_alim\\storage\\logs/laravel.log',
        'level' => 'debug',
        'days' => 14,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'debug',
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'formatter' => NULL,
        'with' => 
        array (
          'stream' => 'php://stderr',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'debug',
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'debug',
      ),
      'null' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\NullHandler',
      ),
      'emergency' => 
      array (
        'path' => 'C:\\inetpub\\wwwplatforms\\_app_alim\\storage\\logs/laravel.log',
      ),
    ),
  ),
  'mail' => 
  array (
    'default' => 'smtp',
    'mailers' => 
    array (
      'smtp' => 
      array (
        'transport' => 'smtp',
        'host' => 'webmail.exercito.pt',
        'port' => '587',
        'encryption' => 'tls',
        'username' => 'exercito\\05802319',
        'password' => 'Codelyoko3*',
        'timeout' => NULL,
        'auth_mode' => NULL,
      ),
      'ses' => 
      array (
        'transport' => 'ses',
      ),
      'mailgun' => 
      array (
        'transport' => 'mailgun',
      ),
      'postmark' => 
      array (
        'transport' => 'postmark',
      ),
      'sendmail' => 
      array (
        'transport' => 'sendmail',
        'path' => '/usr/sbin/sendmail -bs',
      ),
      'log' => 
      array (
        'transport' => 'log',
        'channel' => NULL,
      ),
      'array' => 
      array (
        'transport' => 'array',
      ),
    ),
    'from' => 
    array (
      'address' => 'cpess.unap.informatica@exercito.pt',
      'name' => 'GESTÃO DE ALIMENTAÇÃO',
    ),
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => 'C:\\inetpub\\wwwplatforms\\_app_alim\\resources\\views/vendor/mail',
      ),
    ),
    'stream' => 
    array (
      'ssl' => 
      array (
        'allow_self_signed' => true,
        'verify_peer' => false,
        'verify_peer_name' => false,
      ),
    ),
  ),
  'queue' => 
  array (
    'default' => 'sync',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => 0,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => NULL,
        'secret' => NULL,
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'your-queue-name',
        'suffix' => NULL,
        'region' => 'us-east-1',
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => NULL,
      ),
    ),
    'failed' => 
    array (
      'driver' => 'database-uuids',
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'secure-headers' => 
  array (
    'server' => '',
    'x-content-type-options' => 'nosniff',
    'x-download-options' => 'noopen',
    'x-frame-options' => 'sameorigin',
    'x-permitted-cross-domain-policies' => 'none',
    'x-powered-by' => '',
    'x-xss-protection' => '1; mode=block',
    'referrer-policy' => 'no-referrer',
    'clear-site-data' => 
    array (
      'enable' => false,
      'all' => false,
      'cache' => true,
      'cookies' => true,
      'storage' => true,
      'executionContexts' => true,
    ),
    'hsts' => 
    array (
      'enable' => false,
      'max-age' => 31536000,
      'include-sub-domains' => false,
      'preload' => false,
    ),
    'expect-ct' => 
    array (
      'enable' => false,
      'max-age' => 2147483648,
      'enforce' => false,
      'report-uri' => NULL,
    ),
    'permissions-policy' => 
    array (
      'enable' => true,
      'accelerometer' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'ambient-light-sensor' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'autoplay' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'battery' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'camera' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'cross-origin-isolated' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'display-capture' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'document-domain' => 
      array (
        'none' => false,
        '*' => true,
        'self' => false,
        'origins' => 
        array (
        ),
      ),
      'encrypted-media' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'execution-while-not-rendered' => 
      array (
        'none' => false,
        '*' => true,
        'self' => false,
        'origins' => 
        array (
        ),
      ),
      'execution-while-out-of-viewport' => 
      array (
        'none' => false,
        '*' => true,
        'self' => false,
        'origins' => 
        array (
        ),
      ),
      'fullscreen' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'geolocation' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'gyroscope' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'magnetometer' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'microphone' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'midi' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'navigation-override' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'payment' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'picture-in-picture' => 
      array (
        'none' => false,
        '*' => true,
        'self' => false,
        'origins' => 
        array (
        ),
      ),
      'publickey-credentials-get' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'screen-wake-lock' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'sync-xhr' => 
      array (
        'none' => false,
        '*' => true,
        'self' => false,
        'origins' => 
        array (
        ),
      ),
      'usb' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'web-share' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
      'xr-spatial-tracking' => 
      array (
        'none' => false,
        '*' => false,
        'self' => true,
        'origins' => 
        array (
        ),
      ),
    ),
    'csp' => 
    array (
      'enable' => true,
      'report-only' => false,
      'report-to' => '',
      'report-uri' => 
      array (
      ),
      'block-all-mixed-content' => false,
      'upgrade-insecure-requests' => false,
      'base-uri' => 
      array (
      ),
      'child-src' => 
      array (
      ),
      'connect-src' => 
      array (
      ),
      'default-src' => 
      array (
      ),
      'font-src' => 
      array (
      ),
      'form-action' => 
      array (
      ),
      'frame-ancestors' => 
      array (
      ),
      'frame-src' => 
      array (
      ),
      'img-src' => 
      array (
      ),
      'manifest-src' => 
      array (
      ),
      'media-src' => 
      array (
      ),
      'navigate-to' => 
      array (
        'unsafe-allow-redirects' => false,
      ),
      'object-src' => 
      array (
      ),
      'plugin-types' => 
      array (
      ),
      'prefetch-src' => 
      array (
      ),
      'require-trusted-types-for' => 
      array (
        'script' => false,
      ),
      'sandbox' => 
      array (
        'enable' => false,
        'allow-downloads-without-user-activation' => false,
        'allow-forms' => false,
        'allow-modals' => false,
        'allow-orientation-lock' => false,
        'allow-pointer-lock' => false,
        'allow-popups' => false,
        'allow-popups-to-escape-sandbox' => false,
        'allow-presentation' => false,
        'allow-same-origin' => false,
        'allow-scripts' => false,
        'allow-storage-access-by-user-activation' => false,
        'allow-top-navigation' => false,
        'allow-top-navigation-by-user-activation' => false,
      ),
      'script-src' => 
      array (
        'none' => false,
        'self' => false,
        'report-sample' => false,
        'allow' => 
        array (
        ),
        'schemes' => 
        array (
        ),
        'unsafe-inline' => false,
        'unsafe-eval' => false,
        'unsafe-hashes' => false,
        'strict-dynamic' => false,
        'hashes' => 
        array (
          'sha256' => 
          array (
          ),
          'sha384' => 
          array (
          ),
          'sha512' => 
          array (
          ),
        ),
      ),
      'script-src-attr' => 
      array (
      ),
      'script-src-elem' => 
      array (
      ),
      'style-src' => 
      array (
      ),
      'style-src-attr' => 
      array (
      ),
      'style-src-elem' => 
      array (
      ),
      'trusted-types' => 
      array (
        'enable' => false,
        'allow-duplicates' => false,
        'default' => false,
        'policies' => 
        array (
        ),
      ),
      'worker-src' => 
      array (
      ),
    ),
  ),
  'services' => 
  array (
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
      'endpoint' => 'api.mailgun.net',
    ),
    'postmark' => 
    array (
      'token' => NULL,
    ),
    'ses' => 
    array (
      'key' => NULL,
      'secret' => NULL,
      'region' => 'us-east-1',
    ),
  ),
  'session' => 
  array (
    'driver' => 'cookie',
    'lifetime' => '15',
    'expire_on_close' => true,
    'encrypt' => true,
    'files' => 'C:\\inetpub\\wwwplatforms\\_app_alim\\storage\\framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'gestao_de_alimentacao_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => NULL,
    'http_only' => true,
    'same_site' => 'lax',
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => 'C:\\inetpub\\wwwplatforms\\_app_alim\\resources\\views',
    ),
    'compiled' => 'C:\\inetpub\\wwwplatforms\\_app_alim\\storage\\framework\\views',
  ),
  'dompdf' => 
  array (
    'show_warnings' => false,
    'orientation' => 'portrait',
    'defines' => 
    array (
      'font_dir' => 'C:\\inetpub\\wwwplatforms\\_app_alim\\storage\\fonts/',
      'font_cache' => 'C:\\inetpub\\wwwplatforms\\_app_alim\\storage\\fonts/',
      'temp_dir' => 'C:\\Users\\05802319\\AppData\\Local\\Temp',
      'chroot' => 'C:\\inetpub\\wwwplatforms\\_app_alim',
      'enable_font_subsetting' => false,
      'pdf_backend' => 'CPDF',
      'default_media_type' => 'screen',
      'default_paper_size' => 'a4',
      'default_font' => 'serif',
      'dpi' => 96,
      'enable_php' => false,
      'enable_javascript' => true,
      'enable_remote' => true,
      'font_height_ratio' => 1.1,
      'enable_html5_parser' => false,
    ),
  ),
  'flare' => 
  array (
    'key' => NULL,
    'reporting' => 
    array (
      'anonymize_ips' => true,
      'collect_git_information' => false,
      'report_queries' => true,
      'maximum_number_of_collected_queries' => 200,
      'report_query_bindings' => true,
      'report_view_data' => true,
      'grouping_type' => NULL,
      'report_logs' => true,
      'maximum_number_of_collected_logs' => 200,
      'censor_request_body_fields' => 
      array (
        0 => 'password',
      ),
    ),
    'send_logs_as_events' => true,
    'censor_request_body_fields' => 
    array (
      0 => 'password',
    ),
  ),
  'ignition' => 
  array (
    'editor' => 'phpstorm',
    'theme' => 'light',
    'enable_share_button' => true,
    'register_commands' => false,
    'ignored_solution_providers' => 
    array (
      0 => 'Facade\\Ignition\\SolutionProviders\\MissingPackageSolutionProvider',
    ),
    'enable_runnable_solutions' => NULL,
    'remote_sites_path' => '',
    'local_sites_path' => '',
    'housekeeping_endpoint_prefix' => '_ignition',
  ),
  'trustedproxy' => 
  array (
    'proxies' => NULL,
    'headers' => 94,
  ),
  'ide-helper' => 
  array (
    'filename' => '_ide_helper.php',
    'meta_filename' => '.phpstorm.meta.php',
    'include_fluent' => false,
    'include_factory_builders' => false,
    'write_model_magic_where' => true,
    'write_model_external_builder_methods' => true,
    'write_model_relation_count_properties' => true,
    'write_eloquent_model_mixins' => false,
    'include_helpers' => false,
    'helper_files' => 
    array (
      0 => 'C:\\inetpub\\wwwplatforms\\_app_alim/vendor/laravel/framework/src/Illuminate/Support/helpers.php',
    ),
    'model_locations' => 
    array (
      0 => 'app',
    ),
    'ignored_models' => 
    array (
    ),
    'model_hooks' => 
    array (
    ),
    'extra' => 
    array (
      'Eloquent' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Builder',
        1 => 'Illuminate\\Database\\Query\\Builder',
      ),
      'Session' => 
      array (
        0 => 'Illuminate\\Session\\Store',
      ),
    ),
    'magic' => 
    array (
    ),
    'interfaces' => 
    array (
    ),
    'custom_db_types' => 
    array (
    ),
    'model_camel_case_properties' => false,
    'type_overrides' => 
    array (
      'integer' => 'int',
      'boolean' => 'bool',
    ),
    'include_class_docblocks' => false,
    'force_fqn' => false,
    'additional_relation_types' => 
    array (
    ),
    'post_migrate' => 
    array (
    ),
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'alias' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
  ),
);
