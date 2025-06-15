<?php

namespace App\Console\Commands\Database;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create {name?} {user?} {pass?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new MySQL database based on the database config file or the provided name';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $dbName = $this->argument('name') ?: config('database.connections.mysql.database');
        $dbUser = $this->argument('user') ?: 'root';
        $dbPass = $this->argument('pass') ?: '';

        $charset = config('database.connections.mysql.charset', 'utf8mb4');
        $collation = config('database.connections.mysql.collation', 'utf8mb4_unicode_ci');

        $createUser = "CREATE USER IF NOT EXISTS '$dbUser'@'localhost' IDENTIFIED BY '$dbPass';";
        $createDB = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET $charset COLLATE $collation;";
        $setPrivileges = "GRANT ALL PRIVILEGES ON *.* TO '$dbUser'@'localhost' WITH GRANT OPTION;";

        DB::connection('mysql')->statement($createUser);
        DB::connection('mysql')->statement($createDB);
        DB::connection('mysql')->statement($setPrivileges);
    }
}
