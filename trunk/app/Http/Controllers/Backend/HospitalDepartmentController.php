<?php namespace App\Http\Controllers\Backend;

use View;
use Redirect;
use App\Http\Requests;
use App\Models\Hospital;
use App\Models\Department;
use App\Models\DepartmentDisease;
use App\Models\HospitalDepartment;
use App\Http\Controllers\Controller;

use Request;

class HospitalDepartmentController extends BaseController {

    public $perPage = 50;

    /**
     * 所有部门
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

        $departmentList = HospitalDepartment::orderBy($sort[0], $sort[1])->where(function($query){
            $hospital_id = intval(Request::Input('hospital_id', 0));
            if ($hospital_id) {
                $query->where('hospital_id', $hospital_id);
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

        View::share('departmentList', $departmentList);

        return View::make('backend.pages.hospital-department-all');
    }

    /**
     * 新建部门
     */
    public function getNew()
    {
        return View::make('backend.pages.hospital-department-new');
    }

    public function getEdit()
    {
        $hospital_id = Request::Input('hospital_id', 0);

        $departmentInfo = HospitalDepartment::find(Request::Input('department_id', 0));

        $parentList = HospitalDepartment::where('parent_id', 0)->where('hospital_id', $hospital_id)->where('status', 1)->get();

        View::share('parentList', $parentList);
        View::share('hospital_id', $hospital_id);
        View::share('departmentInfo', $departmentInfo);

        return View::make('backend.pages.hospital-department-edit');
    }

    /**
     * 创建部门
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
        $id               = intval(Request::Input('id', 0));
        $name             = filterVar(Request::Input('name', ''));
        $photo            = filterVar(Request::Input('photo', ''));
        $brief            = filterVar(Request::Input('brief', ''));
        $hospital_id      = intval(Request::Input('hospital_id', 0));
        $parent_id        = intval(Request::Input('parent_id', 0));
        $disease_symptoms = filterVar(Request::Input('disease_symptoms', []));

        if (!$name) {
            return '名称不能为空';
        }

        if ($id) {
            HospitalDepartment::where('id', $id)->update([
                'name'        => $name,
                'brief'       => $brief,
                'photo'       => $photo,
                'parent_id'   => $parent_id,
                'hospital_id' => $hospital_id,
            ]);
            // 原来的disease_id 列表
            $old_disease_symptoms     = DepartmentDisease::where('department_id', $id)->lists('disease_id');
            // 新添加的和原来的 取交集
            $already_disease_symptoms = array_intersect($disease_symptoms, $old_disease_symptoms);
            // 原来的需要删除的disease_id
            $delete_disease_symptoms  = array_diff($old_disease_symptoms, $already_disease_symptoms);
            // 新加的disease_id
            $new_disease_symptoms     = array_diff($disease_symptoms, $already_disease_symptoms);

            foreach ($delete_disease_symptoms as $k => $v) {
                DepartmentDisease::where('department_id', $id)->where('disease_id', $v)->delete();
            }
            foreach ($new_disease_symptoms as $k => $v) {
                DepartmentDisease::insertGetId([
                    'department_id' => $id,
                    'disease_id'    => $v,
                ]);
            }
        } else {
            $id = HospitalDepartment::insertGetId([
                'name'        => $name,
                'brief'       => $brief,
                'photo'       => $photo,
                'hospital_id' => $hospital_id,
                'parent_id'   => $parent_id,
                'created_at'  => date('Y-m-d H:i:s')
            ]);
            foreach ($disease_symptoms as $k => $v) {
                DepartmentDisease::insertGetId([
                    'department_id' => $id,
                    'disease_id'    => $v,
                ]);
            }
        }

        // 更新科室数量
        $this->updateHospital($hospital_id);

        return $id;
    }

    /**
     * 删除
     */
    public function postDelete()
    {
        $id = Request::Input('id', 0);
        if ($id) {
            $hospitalDepartment = HospitalDepartment::where('id', $id)->first();
            if ($hospitalDepartment) {
                $hospitalDepartment->status = HospitalDepartment::STATUS_DELETED;
                $hospitalDepartment->save();
                // 更新科室数量
                $this->updateHospital($hospitalDepartment->hospital_id);
            }
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
            HospitalDepartment::where('id', $id)->update(['status' => 1]);
        }
    }

    public function postParentList()
    {
        // parent-list
        $returnList = [];
        $hospitalId = Request::Input('hospital_id');
        $parentList = HospitalDepartment::where('parent_id', 0)->where('hospital_id', $hospitalId)->where('status', 1)->get();
        foreach ($parentList as $k => $v) {
            $tmpArr = [];
            $tmpArr['id'] = $v->id;
            $tmpArr['name'] = $v->name;
            $returnList[] = $tmpArr;
        }

        return $this->json(['error_code' => 0, 'list' => $returnList]);
    }

    /**
     * 更新医院的科室数量
     */
    public function updateHospital($hospital_id = 0)
    {
        if ($hospital_id) {
            $departmentCount = HospitalDepartment::where('hospital_id', $hospital_id)->where('status', HospitalDepartment::STATUS_OK)->count();
            Hospital::where('id', $hospital_id)->update(['department_num' => $departmentCount]);
        }
    }
}
