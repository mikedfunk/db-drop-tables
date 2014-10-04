<?php
/**
 * DbDropTables Interface
 *
 * @package DbDropTables
 * @license MIT License <http://opensource.org/licenses/mit-license.html>
 */
namespace MikeFunk\DbDropTables;

/**
 * DbDropTablesInterface
 *
 * @author Michael Funk <mike@mikefunk.com>
 */
interface DbDropTablesCommandInterface
{

    /**
     * fire the artisan command
     *
     * @return void
     */
    public function fire();
}
