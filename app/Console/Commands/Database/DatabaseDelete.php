<?php

namespace App\Console\Commands\Database;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:delete {name?} {user?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete MYSQL database and user';

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
     */
    public function handle(): void
    {
        if (! ($dbName = $this->argument('name'))) {
            return;
        }

        $dbName = Str::limit($dbName, 32, '');
        $dbUser = Str::limit($this->argument('user'), 32, '') ?: $dbName;

        $deleteUser = "DROP USER IF EXISTS '$dbUser'@'localhost';";
        $deleteDB = "DROP DATABASE IF EXISTS `$dbName`;";

        DB::connection('mysql')->statement($deleteUser);
        DB::connection('mysql')->statement($deleteDB);
    }
}
