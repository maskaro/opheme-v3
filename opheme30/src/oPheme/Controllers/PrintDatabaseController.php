<?php

namespace oPheme\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class PrintDatabaseController extends Controller
{

	public function __construct ()
	{

		ini_set( 'xdebug.var_display_max_depth', 5 );
		
	}

	public function getAll ()
	{
		// Get database name
		$database = DB::getDatabaseName();
		
		// Get the tables
		$tablesDetails = DB::select( "SHOW FULL TABLES IN {$database}" );
		$tables = array();


		foreach ( $tablesDetails as $tableDetails )
		{
			// Get the columns
			$tableName = $tableDetails->{'Tables_in_'.$database};

			// Simple
			//var_dump(DB::connection()->getSchemaBuilder()->getColumnListing($tablename));
			// Detailed
			$tableColumns = DB::select( "SHOW COLUMNS FROM {$tableName}" );



			// Foreign Keys
			$tableForeignKeys = DB::select( "SELECT 
												CONSTRAINT_NAME,
												TABLE_NAME,
												COLUMN_NAME,
												REFERENCED_TABLE_NAME,
												REFERENCED_COLUMN_NAME
											FROM
												INFORMATION_SCHEMA.KEY_COLUMN_USAGE
											WHERE
												TABLE_NAME = '{$tableName}'
													AND TABLE_SCHEMA = '{$database}'
													AND REFERENCED_TABLE_SCHEMA IS NOT NULL" );

			$tables[] = array( 'name' => $tableName, 'columns' => $tableColumns, 'foreignKeys' => $tableForeignKeys );
		}



		//var_dump($tables);
		return View::make( 'PrintDatabase' )->with( 'tables', $tables );
	}

}
