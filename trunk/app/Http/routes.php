<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');
Route::get('admin', 'Backend\UserController@getLogin');

Route::group(['prefix' => 'admin', 'middleware' => 'backendAuth'], function(){
    Route::group(['middleware' => 'backendCsrfToken'], function(){
        Route::controller('user',                'Backend\UserController');
        Route::controller('category',            'Backend\CategoryController');
        Route::controller('disease-symptoms',    'Backend\DiseaseSymptomsController');
        Route::controller('department',          'Backend\DepartmentController');
        Route::controller('department-disease',  'Backend\DepartmentDiseaseController');
        Route::controller('hospital-department', 'Backend\HospitalDepartmentController');
        Route::controller('hospital',            'Backend\HospitalController');
        Route::controller('doctor',              'Backend\DoctorController');
        Route::controller('rating',              'Backend\RatingController');
        Route::controller('doctor-disease',      'Backend\DoctorDiseaseController');
        Route::controller('patient',             'Backend\PatientController');
        Route::controller('reg',                 'Backend\RegController');
        Route::controller('user-group',          'Backend\UserGroupController');
        Route::controller('tag',                 'Backend\TagController');
        Route::controller('message',             'Backend\MessageController');
        Route::controller('option',              'Backend\OptionController');
        Route::controller('feedback',            'Backend\FeedbackController');
        Route::controller('scene',               'Backend\SceneController');
        Route::controller('satistics',           'Backend\SatisticsController');
     
    });
    Route::controller('file', 'Backend\FileController');
});
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
