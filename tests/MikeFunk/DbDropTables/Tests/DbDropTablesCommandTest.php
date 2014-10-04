<?php
/**
 * Test DbDropTables methods
 *
 * @package DbDropTables
 * @license MIT License <http://opensource.org/licenses/mit-license.html>
 */
namespace MikeFunk\DbDropTables\Tests;

use App;
use Illuminate\Support\Collection;
use Config;
use DB;
use MikeFunk\DbDropTables\DbDropTables;
use Mockery;
use Schema;
use Orchestra\Testbench\TestCase;

/**
 * DbDropTablesTest
 *
 * @author Michael Funk <mike@mikefunk.com>
 */
class DbDropTablesCommandTest extends TestCase
{

    /**
     * The DbDropTablesCommand class
     *
     * @var DbDropTablesCommandInterface
     */
    protected $dbDropTablesCommand;

    /**
     * fake input
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * fake output
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * DbConnection instance
     *
     * @var \Illuminate\Database\Connection $dbConnection
     */
    protected $dbConnection;

    /**
     * get additional service providers
     *
     * @return array
     */
    protected function getPackageProviders()
    {
        return [
            'MikeFunk\DbDropTables\DbDropTablesServiceProvider'
        ];
    }

    /**
     * construct
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        // mock symfony input
        $ns = 'Symfony\Component\Console\Input\InputInterface';
        $this->input = Mockery::mock($ns);
        $this->input->shouldReceive('bind')
            ->shouldReceive('isInteractive')
            ->shouldReceive('validate');

        // mock symfony output
        $ns = 'Symfony\Component\Console\Output\OutputInterface';
        $this->output = Mockery::mock($ns);
        $this->output->shouldReceive('writeln');

        // mock a db connection
        $ns = '\Illuminate\Database\Connection';
        $this->dbConnection = Mockery::mock($ns);

        // voltron assemble!
        $ns = 'MikeFunk\DbDropTables\DbDropTablesCommand';
        $this->dbDropTablesCommand = $this->app->make($ns);
    }

    /**
     * testFire
     *
     * @dataProvider confirmDataProvider
     * @param bool $confirm
     * @return void
     */
    public function testFire($confirm)
    {
        // some fake data
        $databaseName = 'mydatabase';
        $question = "This will drop all tables in {$databaseName}. Are "
            . "you sure you want to do this? [yes|no]";
        $tableName = 'my-table-name';
        $tables = new Collection([(object) ['TABLE_NAME' => $tableName]]);

        // it should get the database name
        Config::shouldReceive('get')
            ->once()
            ->with('database.connections.mysql.database')
            ->andReturn($databaseName);

        // it should confirm
        // this asks the question
        $ns = 'Symfony\Component\Console\Helper\HelperSet';
        $helperSet = Mockery::mock($ns);
        $helperSet->shouldReceive('get')->andReturnSelf()
            ->shouldReceive('ask')->andReturn($confirm);
        $this->dbDropTablesCommand->setHelperSet($helperSet);

        // only expect all this stuff if they confirm
        if ($confirm) {

            // it should get the db connection
            DB::shouldReceive('connection')
                ->once()
                ->andReturnSelf()

                // the db connection should choose a table
                ->shouldReceive('table')
                ->once()
                ->with('TABLES')
                ->andReturnSelf()

                // it should limit to the current db
                // it should only select tables, ignore views
                ->shouldReceive('where')
                ->twice()
                ->andReturnSelf()

                // it should get the collection
                ->shouldReceive('get')
                ->once()
                ->withNoArgs()
                ->andReturn($tables);

            // this is getting kind of cray...
            // all of the following shit is just to mock Schema
            $schemaBuilderNs = 'Illuminate\Database\Schema\Builder';
            $databaseManager = Mockery::mock(
                'Illuminate\Database\DatabaseManager'
            );
            $databaseManager->shouldReceive('connection')
                ->andReturnSelf()
                ->shouldReceive('getSchemaBuilder')
                ->andReturn($schemaBuilderNs);
            $this->app->instance('db', $databaseManager);
            $schemaBuilder = Mockery::mock($ns);
            $schemaBuilder->shouldReceive('dropIfExists')
                ->once()
                ->with($tableName);
            $this->app->instance($schemaBuilderNs, $schemaBuilder);
        }

        // call and verify
        $actual = $this->dbDropTablesCommand->run($this->input, $this->output);
        $this->assertEquals($expected = 0, $actual);
    }

    /**
     * confirmDataProvider
     *
     * @return array
     */
    public function confirmDataProvider()
    {
        return [
            [$confirm = false],
            [$confirm = true],
        ];
    }
}
