<?php namespace App\Http\Controllers\Backend;

use View;
use Redirect;
use App\Http\Requests;
use App\Models\Department;
use App\Http\Controllers\Controller;

use Request;

class DepartmentController extends BaseController {

    public $perPage = 50;

    /**
     * 所有部门
     *
     * @return Response
     */
    public function getAll()
    {
        $departmentList = Department::orderBy('id', 'DESC')->where(function($query){
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

        return View::make('backend.pages.department-all');
    }

    /**
     * 新建部门
     */
    public function getNew()
    {
        return View::make('backend.pages.department-new');
    }

    public function getEdit()
    {
        $departmentInfo = Department::find(Request::Input('department_id', 0));

        View::share('departmentInfo', $departmentInfo);

        return View::make('backend.pages.department-edit');
    }

    /**
     * 创建部门
     */
    public function postCreate(Request $request)
    {
        $result = $this->save();

        return Redirect::back()->withMessage('添加成功！')->withInput();
    }

    /**
     * 更新
     */
    public function postUpdate()
    {
        $result = $this->save();

        return Redirect::back()->withMessage('更新成功！')->withInput();
    }

    /**
     * 通用保存
     * @return number
     */
    private function save()
    {
        $id    = intval(Request::Input('id', 0));
        $name  = filterVar(Request::Input('name', ''));

        if (!$name) {
            return '名称不能为空';
        }

        if ($id) {
            Department::where('id', $id)->update([
                'name' => $name,
            ]);
        } else {
            $id = Department::insertGetId([
                'name'      => $name,
                'created_at' => date('Y-m-d H:i:s')
            ]);
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
            Department::where('id', $id)->update(['status' => 0]);
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
            Department::where('id', $id)->update(['status' => 1]);
        }
    }
}
