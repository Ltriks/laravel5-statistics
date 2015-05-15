<?php namespace App\Http\Controllers\Backend;

use View;
use Redirect;
use App\Http\Requests;
use App\Models\Patient;
use App\Http\Controllers\Controller;

use Request;

class PatientController extends BaseController {

    public $perPage = 50;

    /**
     * 所有患者
     *
     * @return Response
     */
    public function getAll()
    {
        $patientList = Patient::orderBy('id', 'DESC')->where(function($query){
            // 状态
            $status = Request::Input('status', 1);
            $query->where('status', $status);
            // 关键字
            $keyword = Request::Input('keyword', '');
            if (strlen($keyword)) {
                $query->where('name', 'like', "%{$keyword}%");
            }
        })->paginate($this->perPage);

        View::share('patientList', $patientList);

        return View::make('backend.pages.patient-all');
    }

    /**
     * 添加患者
     */
    public function getNew()
    {
        return View::make('backend.pages.patient-new');
    }

    /**
     * 编辑患者
     */
    public function getEdit()
    {
        $patientInfo = Patient::find(Request::Input('patient_id', 0));

        View::share('patientInfo', $patientInfo);

        return View::make('backend.pages.patient-edit');
    }

    /**
     * 更新患者信息
     */
    public function postCreate()
    {
        $result = $this->save();

        if (!is_numeric($result)) {
            return Redirect::back()->withErrors($result)->withInput();
        }

        return Redirect::back()->withMessage('添加成功！')->withInput();
    }

    /**
     * 更新患者信息
     */
    public function postUpdate()
    {
        $result = $this->save();

        if (!is_numeric($result)) {
            return Redirect::back()->withErrors($result)->withInput();
        }

        return Redirect::back()->withMessage('更新成功！')->withInput();
    }

    /**
     * 通用保存
     * @return number
     */
    private function save()
    {
        $id         = intval(Request::Input('id', 0));
        $name       = filterVar(Request::Input('name', ''));
        $gender     = intval(Request::Input('gender', 0));
        $age        = filterVar(Request::Input('age', ''));
        $cert_type  = intval(Request::Input('cert_type', 0));
        $cert_id    = filterVar(Request::Input('cert_id', ''));
        $brief      = filterVar(Request::Input('brief', ''));


        if (!$name) {
            return '名称不能为空';
        }
        // 身份证号格式验证
        if ($cert_type == Patient::CERT_TYPE_ID_CARD) {
            if (!isCreditNo($cert_id)) {
                return '身份证号格式不正确';
            }
        }
        // 添加年龄判断
        if (!is_numeric($age) || ($age < 0 || $age > 200)) {
            return '年龄必须一个数字，且在0~200之间';
        }
        if ($id) {
            $patientCount = Patient::where('cert_id', $cert_id)->where('status', Patient::STATUS_OK)->where('id', '!=', $id)->count();
        } else {
            $patientCount = Patient::where('cert_id', $cert_id)->where('status', Patient::STATUS_OK)->count();
        }

        if ($patientCount) {
            return '身份证号已存在';
        }

        $data = [
            'name'      => $name,
            'gender'    => $gender,
            'age'       => $age,
            'cert_type' => $cert_type,
            'cert_id'   => $cert_id,
            'brief'     => $brief,
        ];

        if ($id) {
            Patient::where('id', $id)->update($data);
        } else {
            $data['user_id'] = getUserInfo('id');
            $data['created_at'] = date('Y-m-d H:i:s');
            $id = Patient::insertGetId($data);
        }

        return $id;
    }

    /**
     * 删除
     */
    public function postDelete()
    {
        $id = Request::Input('id', 0);
        if ($id) {
            Patient::where('id', $id)->update(['status' => 0]);
        }
        return $this->json(['error_code' => 0]);
    }

    /**
     * 恢复
     */
    public function postRecovery()
    {
        $id = Request::Input('id', 0);
        if ($id) {
            Patient::where('id', $id)->update(['status' => 1]);
        }
    }
}
