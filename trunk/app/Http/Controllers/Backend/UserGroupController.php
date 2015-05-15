<?php namespace App\Http\Controllers\Backend;

use \View;
use \Input;
use \Cache;
use \Config;
use \Request;
use \Redirect;
use \Validator;

use App\Models\User;
use App\Http\Requests;
use App\Models\UserGroup;;
use App\Http\Controllers\Controller;

class UserGroupController extends BaseController {


    /**
     * 查看列表
     *
     * @return Response
     */
    public function getAll()
    {
        $sort = Request::Input('sort', 'id-DESC');
        if (!$sort) {
            $sort = 'id-DESC';
        }
        $sort = explode("-", $sort);

        $keyword = Request::Input('keyword', '');
        if ($keyword) {
            $groups = UserGroup::where('name', 'like', "%{$keyword}%")->orderBy($sort[0], $sort[1])->paginate(15);
        } else {
            $groups = UserGroup::orderBy($sort[0], $sort[1])->paginate(15);
        }
        

        return View::make('backend.pages.user-group-all')->withGroups($groups);
    }

    /**
     * 新建
     *
     * @return Response
     */
    public function getNew()
    {
        return View::make('backend.pages.user-group-new');
    }

    /**
     * 创建
     *
     * @return Redirect
     */
    public function postCreate()
    {
        // $validator = new \Service\Validator\UserGroup;

        // if ($validator->fails()) {
        //     return Redirect::back()->withInput()->withErrors($validator->errors);
        // }

        if (UserGroup::where('name', Input::get('name'))->first()) {
            return Redirect::back()->withInput()->withMessage("名称已经存在！");
        }

        $group = new UserGroup;
        $group->name = Input::get('name');
        $group->permission = join('|', Input::get('permission', array()));

        //更新缓存
        $cacheKey = sprintf(Config::get('cache.keys.user_group_perm'), $group->id);
        Cache::put($cacheKey, $group->permission, 10);

        $group->save();

        return Redirect::back()->withMessage('创建成功！');
    }

    /**
     * 编辑
     *
     * @param integer $id
     *
     * @return Response
     */
    public function getEdit($id)
    {
        $group = UserGroup::findOrFail($id);
        $group->permission = explode('|', $group->permission);

        return View::make('backend.pages.user-group-edit')->withGroup($group);
    }

    /**
     * 更新
     *
     * @param integer $id
     *
     * @return Response
     */
    public function postUpdate($id)
    {
        $group = UserGroup::findOrFail($id);
        // $validator = new \Service\Validator\UserGroup;

        // if ($validator->fails()) {
        //     return Redirect::back()->withInput()->withErrors($validator->errors);
        // }

        if (UserGroup::where('name', Input::get('name'))->where('id', '!=', $group->id)->first()) {
            return Redirect::back()->withInput()->withMessage("名称与其它组重名！");
        }

        $group->name = Input::get('name');
        $group->permission = join('|', Input::get('permission', array()));

        //更新缓存
        $cacheKey = sprintf(Config::get('cache.keys.user_group_perm'), $group->id);
        Cache::put($cacheKey, $group->permission, 10);

        $group->save();

        return Redirect::back()->withMessage('更新成功！');
    }

    /**
     * 删除
     *
     * @param array|integer $id
     *
     * @return Reponse
     */
    public function anyDelete($id)
    {
        if (!is_array($id)) {
            $id = array($id);
        }

        UserGroup::whereIn('id', $id)->delete();

        return Redirect::back()->withMessage('删除成功！');
    }
}