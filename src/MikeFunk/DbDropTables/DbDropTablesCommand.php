<?php
/**
 * Drop all tables in a mysql database
 *
 * @package DbDropTables
 * @license MIT License <http://opensource.org/licenses/mit-license.html>
 */
namespace MikeFunk\DbDropTables;

use Config;
use DB;
use Illuminate\Console\Command;
use Schema;

/**
 * DbDropTables
 *
 * @author Michael Funk <mike@mikefunk.com>
 */
class DbDropTablesCommand extends Command implements DbDropTablesCommandInterface
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db:drop-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all tables in the mysql database';

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
     * @throws PDOException if PDO fails
     * @return int 0|1 exit code
     */
    public function fire()
    {
        // get the configured db in the mysql connection
        $database = Config::get('database.connections.mysql.database');

        // confirm - override with --no-interaction or -n
        $question = "This will drop all tables in {$database}. Are "
            . "you sure you want to do this? [yes|no]";
        if (!$this->confirm($question, true)) {
            $this->comment('Command cancelled.');

            return 0;
        }

        // get tables in the current db
        $tables = DB::connection('mysql_information_schema')
            ->table('TABLES')
            // only for this db
            ->where('TABLE_SCHEMA', $database)
            // do not drop mysql views, only tables
            ->where('TABLE_TYPE', 'BASE TABLE')
            ->get();

        // loop through, notify cli, and drop each table if it exists
        foreach ($tables as $table) {
            $tableName = $table->TABLE_NAME;
            $this->info('dropping table '.$tableName);
            Schema::dropIfExists($tableName);
        }

        // report success
        $this->info("all tables in {$database} dropped!");

        return 0;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
        ];
    }
}
