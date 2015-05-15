<?php namespace App\Http\Controllers\Backend;

use View;
use App\Http\Requests;
use App\Models\Rating;
use App\Http\Controllers\Controller;

use Request;

class RatingController extends BaseController {

    public $perPage = 50;

    /**
     * 所有反馈
     *
     * @return Response
     */
    public function getAll()
    {
        $ratingList = Rating::orderBy('id', 'DESC')->where(function($query){
            // 按医生筛选
            $doctor_id = Request::Input('doctor_id', 0);
            if ($doctor_id) {
                $query->where('doctor_id', $doctor_id);
            }
            
        })->paginate($this->perPage);

        View::share('ratingList', $ratingList);

        return View::make('backend.pages.rating-all');
    }

}
