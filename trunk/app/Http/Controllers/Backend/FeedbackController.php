<?php namespace App\Http\Controllers\Backend;

use View;
use Redirect;
use App\Http\Requests;
use App\Models\Feedback;
use App\Http\Controllers\Controller;

use Request;

class FeedbackController extends BaseController {

    public $perPage = 50;

    /**
     * 所有反馈
     *
     * @return Response
     */
    public function getAll()
    {
        $feedbackList = Feedback::orderBy('id', 'DESC')->where(function($query){
            
        })->paginate($this->perPage);

        View::share('feedbackList', $feedbackList);

        return View::make('backend.pages.feedback-all');
    }

}
