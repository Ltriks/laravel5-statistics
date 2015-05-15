<?php namespace App\Http\Controllers\Backend;

use View;
use Redirect;
use App\Models\Category;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;

class CategoryController extends BaseController {

    public $perPage = 50;

    /**
     * 所有分类
     *
     * @return Response
     */
    public function getAll()
    {
        $categoryList = Category::orderBy('id', 'DESC')->where(function($query){
            // 类型
            $type = Request::Input('type', 1);
            $query->where('type', $type);
            // 状态
            $status = Request::Input('status', 1);
            $query->where('status', $status);
            // 关键字
            $keyword = Request::Input('keyword', '');
            if (strlen($keyword)) {
                $query->where('name', 'like', "%{$keyword}%");
            }
        })->paginate($this->perPage);

        View::share('categoryList', $categoryList);

        return View::make('backend.pages.category-all');
    }

    /**
     * 新建分类
     */
    public function getNew()
    {
        return View::make('backend.pages.category-new');
    }

    public function getEdit()
    {
        $categoryInfo = Category::find(Request::Input('category_id', 0));

        View::share('categoryInfo', $categoryInfo);

        return View::make('backend.pages.category-edit');
    }

    /**
     * 创建分类
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
        $id    = intval(Request::Input('id', 0));
        $type  = intval(Request::Input('type', 0));
        $name  = filterVar(Request::Input('name', ''));
        $icon  = filterVar(Request::Input('icon', ''));
        $sort  = intval(Request::Input('sort', 0));

        if (!$name) {
            return '名称不能为空';
        }

        if ($id) {
            $categoryCount = Category::where('status', Category::STATUS_OK)->where('name', $name)->where('id', '!=', $id)->count();
            if ($categoryCount) {
                return '名称已经存在';
            }
        } else {
            $categoryCount = Category::where('status', Category::STATUS_OK)->where('name', $name)->count();
            if ($categoryCount) {
                return '名称已经存在';
            }
        }

        if ($id) {
            Category::where('id', $id)->update([
                'name' => $name,
                'icon'  => $icon,
                'sort' =>  $sort,
            ]);
        } else {
            $id = Category::insertGetId([
                'name'      => $name,
                'type'       => $type,
                'icon'       => $icon,
                'sort'       =>  $sort,
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
            Category::where('id', $id)->update(['status' => 0]);
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
            Category::where('id', $id)->update(['status' => 1]);
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
        Category::findOrFail($id)->update(array('sort' => $sort));

        return $this->json(['status' => true, 'data' => ['sort' => $sort]]);
    }
}
