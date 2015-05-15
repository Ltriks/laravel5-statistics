<?php namespace App\Http\Controllers\Backend;

use View;
use Redirect;
use App\Models\Tag;
use App\Http\Requests;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\DoctorTag;
use App\Models\DoctorDisease;
use App\Models\DoctorSchedule;
use App\Models\DepartmentDoctor;
use App\Models\DepartmentDisease;
use App\Models\HospitalDepartment;
use App\Models\DoctorDoctorShedule;
use App\Http\Controllers\Controller;

use Request;

class DoctorController extends BaseController {

    public $perPage = 50;

    /**
     * 所有医生
     *
     * @return Response
     */
    public function getAll()
    {
        // 排序
        $sort = Request::Input('sort', 'id-DESC');
        if (!$sort) {
            $sort = 'id-DESC';
        }
        $sort = explode("-", $sort);

        $doctorList = Doctor::orderBy($sort[0], $sort[1])->where(function($query){
            // 医院
            $hospital_id = intval(Request::Input('hospital_id', 0));
            if ($hospital_id) {
                $query->where('hospital_id', $hospital_id);
            }
            $level = intval(Request::Input('level', -1));
            if ($level == Doctor::DOCTOR_LEVEL_1) {
                $query->where('level', $level);
            } else if ($level == Doctor::DOCTOR_LEVEL_2) {
                $query->where('level', $level);
            } else if ($level == Doctor::DOCTOR_LEVEL_3) {
                $query->where('level', $level);
            }
            // 状态
            $status = Request::Input('status', 1);
            $query->where('status', $status);
            // 关键字
            $keyword = Request::Input('keyword', '');
            if (strlen($keyword)) {
                $query->where('name', 'like', "%{$keyword}%");
            }
        })->paginate($this->perPage);

        View::share('doctorList', $doctorList);

        return View::make('backend.pages.doctor-all');
    }

    /**
     * 新建医生
     */
    public function getNew()
    {
        return View::make('backend.pages.doctor-new');
    }

    /**
     * 编辑医生
     */
    public function getEdit()
    {
        $doctorInfo = Doctor::find(Request::Input('doctor_id', 0));

        View::share('doctorInfo', $doctorInfo);

        return View::make('backend.pages.doctor-edit');
    }

    /**
     * 创建医生
     */
    public function postCreate(Request $request)
    {
        $result = $this->save();

        if (!is_numeric($result)) {
            return Redirect::back()->withErrors($result)->withInput();
        }

        return Redirect::back()->withMessage('添加成功！')->withInput();
    }

    /**
     * 更新
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
        $id          = intval(Request::Input('id', 0));
        $gender      = intval(Request::Input('gender', 0));
        $avatar      = filterVar(Request::Input('avatar', ''));
        $level       = filterVar(Request::Input('level', ''));
        $title       = filterVar(Request::Input('title', ''));
        $name        = filterVar(Request::Input('name', ''));
        $hospital_id = intval(Request::Input('hospital_id', 0));
        $brief       = filterVar(Request::Input('brief', ''));
        $experience  = intval(Request::Input('experience', 0));
        $school      = filterVar(Request::Input('school', ''));
        $is_verified = intval(Request::Input('is_verified', 0));
        $price       = Request::Input('price', '0.00');

        $departments = Request::Input('departments', []);

        if (!$name) {
            return '姓名不正确';
        }
        if (!$hospital_id) {
            return '请填写医院';
        }

        $data = [
            'hospital_id' => $hospital_id,
            'name'        => $name,
            'gender'      => $gender,
            'level'       => $level,
            'title'       => $title,
            'experience'  => $experience,
            'school'      => $school,
            'brief'       => $brief,
            'is_verified' => $is_verified,
            'avatar'      => $avatar,
        ];
        if ($departments) {
            $department_id = current($departments);
            // $departmentInfo = HospitalDepartment::find($department_id);
            // if ($departmentInfo->parent_id != 0) {
            //     $departmentInfo = HospitalDepartment::find($departmentInfo->parent_id);
            // }
            // if ($departmentInfo && $departmentInfo->id) {
            //     $data['department_id'] = $departmentInfo->id;
            // }
        } else {
            $department_id = 0;
        }
        if ($price) {
            $data['price'] = $price;
        }

        if ($id) {

            Doctor::where('id', $id)->update($data);
            // 编辑时候的操作
            // 原来的department_id 列表
            $old_departments     = DepartmentDoctor::where('doctor_id', $id)->lists('department_id');
            // 新添加的和原来的 取交集
            $already_departments = array_intersect($departments, $old_departments);
            // 原来的需要删除的department_id
            $delete_departments  = array_diff($old_departments, $already_departments);
            // 新加的department_id
            $new_departments     = array_diff($departments, $already_departments);

            foreach ($delete_departments as $k => $v) {
                DepartmentDoctor::where('doctor_id', $id)->where('department_id', $v)->delete();
            }
            foreach ($new_departments as $k => $v) {
                DepartmentDoctor::insertGetId([
                    'doctor_id' => $id,
                    'department_id'    => $v,
                ]);
            }

        } else {

            $data['created_at'] = date('Y-m-d H:i:s');
            $id = Doctor::insertGetId($data);
            // 处理科室
            foreach ($departments as $k => $v) {
                DepartmentDoctor::insertGetId([
                    'doctor_id' => $id,
                    'department_id' => $v
                ]);
            }

        }

        DoctorDisease::where('doctor_id', $id)->delete();
        $departmentDiseaseList = DepartmentDisease::where('department_id', $department_id)->get();
        foreach ($departmentDiseaseList as $k => $v) {
            DoctorDisease::insertGetId([
                'doctor_id'  => $id,
                'disease_id' => $v->disease_id,
            ]);
        }

        // 获取标签
        $tags      = Request::Input('tags', []);
        // 原来的tag_id 列表
        $old_tags     = DoctorTag::where('doctor_id', $id)->lists('tag_id');
        // 新添加的和原来的 取交集
        $already_tags = array_intersect($tags, $old_tags);
        // 原来的需要删除的tag_id
        $delete_tags  = array_diff($old_tags, $already_tags);
        // 新加的tag_id
        $new_tags     = array_diff($tags, $already_tags);

        foreach ($delete_tags as $k => $v) {
            DoctorTag::where('doctor_id', $id)->where('tag_id', $v)->delete();
        }
        foreach ($new_tags as $k => $v) {
            DoctorTag::insertGetId([
                'doctor_id' => $id,
                'tag_id'    => $v,
            ]);
        }
        $doctor_tags = array_merge($new_tags, $already_tags);
        // 医生医生标签
        Doctor::where('id', $id)->update(['tags' => Tag::getNameList($doctor_tags)]);
        // 更新医生数量
        $this->updateHospital($hospital_id);
        // 更新排班
        $this->postUpdateSchedule($id);

        return $id;
    }

    /**
     * 删除
     */
    public function postDelete()
    {
        $id = Request::Input('id', 0);
        if ($id) {
            Doctor::where('id', $id)->update(['status' => 0]);
            // 更新医生数量
            $this->updateHospital($hospital_id);
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
            Doctor::where('id', $id)->update(['status' => 1]);
        }
    }

    /**
     * 排期
     */
    public function getEditSchedule()
    {

        $doctorInfo = Doctor::find(Request::Input('doctor_id', 1));

        View::share('doctorInfo', $doctorInfo);

        return View::make('backend.pages.doctor-edit-schedule');
    }

    /**
     * 更新排期
     */
    private function postUpdateSchedule($doctorId = 0)
    {
        // update-schedule
        if (!$doctorId) {
            $doctorId = intval(Request::Input('id', 0));
        }

        $weekList = Request::Input('week', []);

        $doctorInfo = Doctor::getInfo($doctorId);

        if (is_string($doctorInfo)) {
            return View::make('404');
        }
        $doctorScheduleCout = DoctorSchedule::where('doctor_id', $doctorId)->count();
        if ($doctorScheduleCout < 7) {
            DoctorSchedule::where('doctor_id', $doctorId)->delete();
        }
        if ($doctorScheduleCout < 7 || $doctorScheduleCout == 0) {
            foreach ($weekList as $k => $v) {
                DoctorSchedule::insertGetId([
                    'doctor_id'   => $doctorId,
                    'day'         => $k,
                    'work_status' => $v,
                    'created_at'  => date('Y-m-d H:i:s')
                ]);
            }
        } else {
            foreach ($weekList as $k => $v) {
                DoctorSchedule::where('doctor_id', $doctorId)
                              ->where('day', $k)
                              ->update(['work_status' => $v]);
            }
        }

        return Redirect::back()->withMessage('编辑成功！')->withInput();

    }

    /**
     * 更新医院的医生数量
     */
    public function updateHospital($hospital_id = 0)
    {
        if ($hospital_id) {
            $doctorCount = Doctor::where('hospital_id', $hospital_id)->where('status', Doctor::STATUS_OK)->count();
            Hospital::where('id', $hospital_id)->update(['doctor_num' => $doctorCount]);
        }
    }

}
