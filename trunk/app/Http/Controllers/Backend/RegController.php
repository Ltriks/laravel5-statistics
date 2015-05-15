<?php namespace App\Http\Controllers\Backend;

use View;
use Redirect;
use App\Models\Reg;
use App\Http\Requests;
use App\Models\Doctor;
use App\Models\Patient;
use App\Http\Controllers\Controller;

use Request;

class RegController extends BaseController {

    public $perPage = 50;

    /**
     * 所有挂号单
     *
     * @return Response
     */
    public function getAll()
    {
        $regList = Reg::orderBy('id', 'DESC')->where(function($query){
            $state = Request::Input('state', -1);
            if ($state == Reg::ORDER_STATE_PENDING) {
                $query->where('state', Reg::ORDER_STATE_PENDING);

            } else if ($state == Reg::ORDER_STATE_SUCCESS) {
                $query->where('state', Reg::ORDER_STATE_SUCCESS);

            } else if ($state == Reg::ORDER_STATE_FAILED) {
                $query->where('state', Reg::ORDER_STATE_FAILED);
                
            } else if ($state == Reg::ORDER_STATE_WAIT_COMMENT) {
                $query->where('state', Reg::ORDER_STATE_WAIT_COMMENT);
                
            } else if ($state == Reg::ORDER_STATE_FINISHED) {
                $query->where('state', Reg::ORDER_STATE_FINISHED);
                
            } else if ($state == Reg::ORDER_STATE_CANCELLED) {
                $query->where('state', Reg::ORDER_STATE_CANCELLED);

            }
            if ($hospital_id = intval(Request::Input('hospital_id', 0))) {
                $query->where('hospital_id', $hospital_id);
            }
            // 医生
            $doctor_id = intval(Request::Input('doctor_id', 0));
            if ($doctor_id) {
                $query->where('doctor_id', $doctor_id);
            }
            // 状态
            $status = Request::Input('status', 1);
            $query->where('status', $status);
            // 病人姓名
            if ($patient_name = Request::Input('patient_name', '')) {
                $patientIds = Patient::where('name', 'like', "%{$patient_name}%")->lists('id');
                if ($patientIds) {
                    $query->whereIn('patient_id', $patientIds);
                } else {
                    $query->where('id', '-1');
                }
            }
            // 医生姓名
            if ($doctor_name = Request::Input('doctor_name', '')) {
                $doctorIds = Doctor::where('name', 'like', "%{$doctor_name}%")->lists('id');
                if ($doctorIds) {
                    $query->whereIn('doctor_id', $doctorIds);
                } else {
                    $query->where('id', '-1');
                }
            }
        })->paginate($this->perPage);

        View::share('regList', $regList);

        return View::make('backend.pages.reg-all');
    }

    /**
     * 添加挂号单
     */
    public function getNew()
    {
        return View::make('backend.pages.reg-new');
    }

    /**
     * 编辑挂号单
     */
    public function getEdit()
    {
        $regInfo = Reg::find(Request::Input('reg_id', 0));

        View::share('regInfo', $regInfo);

        return View::make('backend.pages.reg-edit');
    }

    /**
     * 更新挂号单信息
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
     * 更新挂号单信息
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
        $age        = intval(Request::Input('age', 0));
        $cert_type  = intval(Request::Input('cert_type', 0));
        $cert_id    = filterVar(Request::Input('cert_id', ''));
        $brief      = filterVar(Request::Input('brief', ''));


        if (!$name) {
            return '名称不能为空';
        }
        if (!$cert_id) {
            return '证件内容不正确';
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
            Reg::where('id', $id)->update($data);
        } else {
            $data['user_id'] = getUserInfo('id');
            $data['created_at'] = date('Y-m-d H:i:s');
            $id = Reg::insertGetId($data);
        }

        return $id;
    }

    /**
     * 更新状态
     */
    public function postUpdateStatus()
    {
        $msg    = '';
        $reg_id = intval(Request::Input('reg_id', 0));
        $state  = intval(Request::Input('state', 0));

        $regInfo = Reg::find($reg_id);

        if (!$regInfo) {
            return $this->json(['error_code' => 1, 'error_desc' => '不存在挂号单']);
        }

        if ($state == Reg::ORDER_STATE_SUCCESS) {
            $regInfo->state = Reg::ORDER_STATE_SUCCESS;
            $msg = '预约成功->操作成功';

        } else if ($state == Reg::ORDER_STATE_FAILED) {
            $regInfo->state = Reg::ORDER_STATE_FAILED;
            $msg = '预约失败->操作成功';

        } else if ($state == Reg::ORDER_STATE_CANCELLED) {
            $regInfo->state = Reg::ORDER_STATE_CANCELLED;
            $msg = '取消预约->操作成功';

        } else {
            return $this->json(['error_code' => 1, 'error_desc' => '操作失败.']);
        }

        $regInfo->save();

        return $this->json(['error_code' => 0, 'msg' => $msg]);
    }

    /**
     * 删除
     */
    public function postDelete()
    {
        $id = Request::Input('id', 0);
        if ($id) {
            Reg::where('id', $id)->update(['status' => 0]);
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
            Reg::where('id', $id)->update(['status' => 1]);
        }
    }
}
