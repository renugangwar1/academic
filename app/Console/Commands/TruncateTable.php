<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TruncateTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'truncate:table {tableName : The name of the table to truncate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate desire table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tableName = $this->argument('tableName');
        
        if (empty($tableName)) {
            $this->error('Please provide a valid table name.');
            return;
        }

        // Additional validation or processing if needed

        DB::table($tableName)->truncate();

        $this->info("Table '$tableName' truncated successfully.");
    }
}
