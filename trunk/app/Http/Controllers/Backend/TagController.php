<?php namespace App\Http\Controllers\Backend;

use View;
use Redirect;
use App\Models\Tag;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;

class TagController extends BaseController {

    public $perPage = 50;

    /**
     * 所有标签
     *
     * @return Response
     */
    public function getAll()
    {
        $tagList = Tag::orderBy('id', 'DESC')->where(function($query){
            $keyword = Request::Input('keyword', '');
            if ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            }
            // 状态
            $status = Request::Input('status', 1);
            $query->where('status', $status);
        })->paginate($this->perPage);

        View::share('tagList', $tagList);

        return View::make('backend.pages.tag-all');
    }

    /**
     * 新建标签
     */
    public function getNew()
    {
        return View::make('backend.pages.tag-new');
    }

    public function getEdit()
    {
        $tagInfo = Tag::find(Request::Input('tag_id', 0));

        View::share('tagInfo', $tagInfo);

        return View::make('backend.pages.tag-edit');
    }

    /**
     * 创建标签
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
        $name  = filterVar(Request::Input('name', ''));

        if (!$name) {
            return '名称不能为空';
        }

        if ($id) {
            $tagCount = Tag::where('name', $name)->where('status', 1)->where('id', '!=', $id)->count();
            if ($tagCount) {
                return '名称有重复';
            }
            Tag::where('id', $id)->update([
                'name' => $name,
            ]);
        } else {
            $tagCount = Tag::where('name', $name)->where('status', 1)->count();
            if ($tagCount) {
                return '名称已存在';
            }
            $id = Tag::insertGetId([
                'name'      => $name,
                'type'      => Tag::OBJECT_TYPE_DOCTOR,
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
            Tag::where('id', $id)->update(['status' => 0]);
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
            Tag::where('id', $id)->update(['status' => 1]);
        }
    }
}
