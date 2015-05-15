<?php namespace App\Http\Controllers\Backend;

use View;
use Redirect;
use App\Http\Requests;
use App\Models\Hospital;
use App\Http\Controllers\Controller;

use Request;

class HospitalController extends BaseController {

    public $perPage = 50;

    /**
     * 所有医院
     *
     * @return Response
     */
    public function getAll()
    {
        $hospitalList = Hospital::orderBy('id', 'DESC')->where(function($query){
            // 等级
            $level = Request::Input('level', -1);
            if ($level > 0) {
                $query->where('level', $level);
            }
            // 类型
            $type = Request::Input('type', -1);
            if ($type > 0) {
                $query->where('type', $type);
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

        View::share('hospitalList', $hospitalList);

        return View::make('backend.pages.hospital-all');
    }

    /**
     * 新建医院
     */
    public function getNew()
    {
        return View::make('backend.pages.hospital-new');
    }

    public function getEdit()
    {
        $hospitalInfo = Hospital::find(Request::Input('hospital_id', 0));

        View::share('hospitalInfo', $hospitalInfo);

        return View::make('backend.pages.hospital-edit');
    }

    /**
     * 创建医院
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
        $id       = intval(Request::Input('id', 0));
        $type     = intval(Request::Input('type', 0));
        $level    = filterVar(Request::Input('level', ''));
        $name     = filterVar(Request::Input('name', ''));
        $image    = filterVar(Request::Input('image', ''));
        $address  = filterVar(Request::Input('address', ''));
        $intro    = filterVar(Request::Input('intro', ''));
        $sort     = intval(Request::Input('sort', 0));
        $price    = Request::Input('price', 0);

        $lon = $lat = '';

        if (!$name) {
            return '名称不能为空';
        }

        if (!$id) {
            $hospitalCount = Hospital::where('name', $name)->count();
            if ($hospitalCount) {
                return '名称已经存在';
            }
        }
        
        if (!$address) {
            return '地址不能为空';
        }
        if ($sort < 0 && $sort >10000) {
            return '排序不正确';
        }

        if ($price < 0) {
            return '价格不正确';
        }

        $data = [
            'name'     => $name,
            'level'    => $level,
            'type'     => $type,
            'lon'      => $lon,
            'lat'      => $lat,
            'sort'     => $sort,
            'price'    => $price,
            'intro'    => $intro,
            'address'  => $address,
            'image'    => $image,
        ];

        if ($id) {
            Hospital::where('id', $id)->update($data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $id = Hospital::insertGetId($data);
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
            Hospital::where('id', $id)->update(['status' => 0]);
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
            Hospital::where('id', $id)->update(['status' => 1]);
        }
    }

    /**
     * 修改排序
     *
     * @param integer $id
     * @param integer $sort
     *
     * @return Response
     */
    public function getUpdateSort($id, $sort)
    {
        $sort = abs($sort) > 10000 ? 10000 : abs($sort);
        Hospital::findOrFail($id)->update(array('sort' => $sort));

        return $this->json(['status' => true, 'data' => ['sort' => $sort]]);
    }
}
