<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateCompanypermissionTable
	extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up ()
	{
		Schema::create( 'companypermission', function(Blueprint $table)
		{
			// InnoDB
			$table->engine = 'InnoDB';

			// Columns
			$table->char( 'id', 36 )->primary(); // Primary Key
			$table->char( 'company_id', 36 ); // Foreign Unique Composite Key
			$table->char( 'permission_id', 36 ); // Foreign Unique Composite Key
//			$table->timestamp('Created')->default(DB::raw('CURRENT_TIMESTAMP'));
//			$table->timestamp('Modified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
//			$table->timestamp('Deleted')->nullable()->default(DB::raw('NULL'));
			$table->timestamps();
			$table->softDeletes();

			// Composite Key (Indexing / Linking)
			$table->unique( array(
				'company_id',
				'permission_id' ), 'company_id_permission_id_unique' ); // Unique Composite Key
			// Foreign Keys (Indexing / Linking)
			$table->index( 'company_id', 'company_id_idx' );
			$table->foreign( 'company_id', 'company_id_companypermission_foreign' )->references( 'id' )->on( 'company' )->onDelete( 'cascade' )->onUpdate( 'restrict' );

			$table->index( 'permission_id', 'permission_id_idx' );
			$table->foreign( 'permission_id', 'permission_id_companypermission_foreign' )->references( 'id' )->on( 'permission' )->onDelete( 'cascade' )->onUpdate( 'restrict' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down ()
	{
		Schema::table( 'companypermission', function($table)
		{
			$table->dropForeign( 'company_id_companypermission_foreign' );
			$table->dropForeign( 'permission_id_companypermission_foreign' );
		} );

		Schema::drop( 'companypermission' );
	}

}
