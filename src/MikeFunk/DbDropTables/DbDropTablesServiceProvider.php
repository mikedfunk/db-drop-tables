<?php
/**
 * Laravel Service Provider for DbDropTables
 *
 * @package DbDropTables
 * @license MIT License <http://opensource.org/licenses/mit-license.html>
 */
namespace MikeFunk\DbDropTables;

use Illuminate\Support\ServiceProvider;

/**
 * DbDropTablesServiceProvider
 *
 * @author Michael Funk <mike@mikefunk.com>
 */
class DbDropTablesServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('mike-funk/db-drop-tables');
    }

    /**
     * Bind the interface to the implementation
     *
     * @return void
     */
    public function register()
    {
        // register db drop tables command
        $this->app->bind(
            'db.drop-tables',
            'MikeFunk\DbDropTables\DbDropTablesCommand'
        );
        $this->commands('db.drop-tables');
    }
}
