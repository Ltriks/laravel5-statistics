<?php namespace App\Http\Controllers\Backend;

use View;
use Redirect;
use App\Http\Requests;
use App\Models\Doctor;
use App\Models\DoctorDisease;
use App\Http\Controllers\Controller;

use Request;

class DoctorDiseaseController extends BaseController {

    public $perPage = 50;

    public function getEdit()
    {
        $doctor_id = intval(Request::Input('doctor_id', 0));

        $doctorInfo = Doctor::find($doctor_id);

        View::share('doctorInfo', $doctorInfo);

        return View::make('backend.pages.doctor-disease-edit');
    }


    /**
     * 更新
     */
    public function postUpdate()
    {
        $doctor_id   = intval(Request::Input('doctor_id', 0));
        $disease_symptoms = Request::Input('disease_symptoms', []);

        // 原来的disease_id 列表
        $old_disease_symptoms     = DoctorDisease::where('doctor_id', $doctor_id)->lists('disease_id');
        // 新添加的和原来的 取交集
        $already_disease_symptoms = array_intersect($disease_symptoms, $old_disease_symptoms);
        // 原来的需要删除的disease_id
        $delete_disease_symptoms  = array_diff($old_disease_symptoms, $already_disease_symptoms);
        // 新加的disease_id
        $new_disease_symptoms     = array_diff($disease_symptoms, $already_disease_symptoms);

        foreach ($delete_disease_symptoms as $k => $v) {
            DoctorDisease::where('doctor_id', $doctor_id)->where('disease_id', $v)->delete();
        }
        foreach ($new_disease_symptoms as $k => $v) {
            DoctorDisease::insertGetId([
                'doctor_id'  => $doctor_id,
                'disease_id' => $v,
            ]);
        }

        return Redirect::back()->withMessage('更新成功！')->withInput();
    }

}
