<?php namespace App\Providers;

use DB;
use Log;
use Illuminate\Support\ServiceProvider;

class SetupServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		require_once app_path().'/Helper/common.php';

		DB::listen(function($sql, $bindings, $time)
		{
		    Log::info($sql . ", with[" . join(',', $bindings) ."], times[{$time}]");
		});
	}

}
