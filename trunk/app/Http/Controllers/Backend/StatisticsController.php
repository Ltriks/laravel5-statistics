<?php namespace App\Http\Controllers\Backend;

use View;
use Input;
use Session;
use Request;
use Redirect;
use Response;
use App\Models\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class StatisticsController extends BaseController {

	public function user()
	{
		echo 'this is user ';
	}

}