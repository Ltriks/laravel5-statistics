<?php namespace App\Http\Controllers\Backend;

use View;
use Redirect;
use App\Http\Requests;
use App\Models\DepartmentDoctor;
use App\Models\DepartmentDisease;
use App\Models\HospitalDepartment;
use App\Http\Controllers\Controller;

use Request;

class DepartmentDiseaseController extends BaseController {

    public $perPage = 50;

    public function getEdit()
    {
        $department_id = intval(Request::Input('department_id', 0));

        $departmentInfo = HospitalDepartment::find($department_id);

        View::share('departmentInfo', $departmentInfo);

        return View::make('backend.pages.department-disease-edit');
    }


    /**
     * 更新
     */
    public function postUpdate()
    {
        $department_id   = intval(Request::Input('department_id', 0));
        $disease_symptoms = Request::Input('disease_symptoms', []);

        // 原来的disease_id 列表
        $old_disease_symptoms     = DepartmentDisease::where('department_id', $department_id)->lists('disease_id');
        // 新添加的和原来的 取交集
        $already_disease_symptoms = array_intersect($disease_symptoms, $old_disease_symptoms);
        // 原来的需要删除的disease_id
        $delete_disease_symptoms  = array_diff($old_disease_symptoms, $already_disease_symptoms);
        // 新加的disease_id
        $new_disease_symptoms     = array_diff($disease_symptoms, $already_disease_symptoms);

        foreach ($delete_disease_symptoms as $k => $v) {
            DepartmentDisease::where('department_id', $department_id)->where('disease_id', $v)->delete();
        }
        foreach ($new_disease_symptoms as $k => $v) {
            DepartmentDisease::insertGetId([
                'department_id'  => $department_id,
                'disease_id' => $v,
            ]);
        }

        return Redirect::back()->withMessage('更新成功！')->withInput();
    }

}
