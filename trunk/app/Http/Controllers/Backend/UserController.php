<?php namespace App\Http\Controllers\Backend;

use View;
use Input;
use Session;
use Request;
use Redirect;
use Response;
use App\Models\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends BaseController {

    private $perPage = 50;



    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getLogin()
    {
        if (isAdminLogin()) {
            return View::make('backend.pages.home');
        } else {
            return View::make('backend.pages.login');
        }
    }

    /**
     * 登出
     */
    public function getLogout()
    {
        Session::forget('user_info');
        return View::make('backend.pages.login');
    }

    public function postLogin()
    {
        $username = filterVar(Request::Input('username', ''));
        $password = filterVar(Request::Input('password', ''));

        if (!$username || !$password) {
            return Response::json(array(
                      'login_status' => 'invalid',
                      'error_code'   => 0,
                     ));
        }

        $userInfo = User::where('username', $username)
                         ->where('password', generatePassword($password))
                         ->where('status', User::STATUS_OK)
                         ->where('role', User::ROLE_ADMIN)->first();
        if (count($userInfo)) {
            sessionUserInfo($userInfo);
            return Response::json(array(
                    'login_status' => 'success',
                    'error_code'   => 0,
                    'redirect_url' => url('admin'),
            ));
        } else {
            return Response::json(array(
                    'login_status' => 'invalid',
                    'error_code'   => 0,
            ));
        }
    }

    /**
     * 所有用户
     *
     * @return Response
     */
    public function getAll()
    {
        $regTimeRange = Input::get('time_range', '');
        if ($regTimeRange) {
            preg_match_all('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2} - \d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $regTimeRange, $result);
            if (!isset($result[0][0]) || !$result[0][0]) {
                return Redirect::back()->withMessage('注册时间格式不正确')->withColor('danger')->withInput();
            }
        }
        $userList = User::orderBy('id', 'DESC')->where(function($query){
            //状态
            if (!empty($status = Input::get('status', 1))) {
                $query->whereStatus($status);
            }

            //角色
            if (!empty($role = Input::get('role'))) {
                $query->whereRole($role);
            }

            //时间
            if (!empty($regTimeRange = Input::get('time_range'))) {
                $times = explode(' - ', $regTimeRange);
                $query->whereBetween('created_at', $times);
            }

            //关键字
            if (!empty($keyword = Input::get('keyword'))) {
                $query->whereRaw("(CONCAT(`id`,`name`,`username`,`mobile`) LIKE '%{$keyword}%')");
            }
        })->paginate($this->perPage);

        //关键字搜索用户时用
        if (Request::wantsJson()) {
            return $userList->getCollection()->toArray();
        }

        View::share('userList', $userList);

        return View::make('backend.pages.user-all');
    }

    /**
     * 新建
     */
    public function getNew()
    {
        return View::make('backend.pages.user-new');
    }

    public function getEdit()
    {
        $id = intval(Request::Input('user_id', 0));
        $userInfo = [];

        if ($id) {
            $userInfo = User::where('id', $id)->first();
        }
        if ($userInfo) {
            View::share('userInfo', $userInfo);
            return View::make('backend.pages.user-edit');
        } else {
            return View::make('404');
        }
    }

    /**
     * 创建用户
     */
    public function postCreate()
    {
        $result = $this->checkUser();
        if ($result == 'ok') {
            $this->save();
            return Redirect::back()->withMessage('添加成功！')->withInput();
        } else {
            return Redirect::back()->withMessage($result)->withColor('danger')->withInput();
        }
    }

    /**
     *更新用户
     */
    public function postUpdate()
    {
        $result = $this->checkUser();
        if ($result == 'ok') {
            $this->save();
            return Redirect::back()->withMessage('更新成功！');
        } else {
            return Redirect::back()->withMessage($result)->withColor('danger');
        }
    }

    /**
     * 检查用户合法性
     * @return string
     */
    private function checkUser()
    {
        $userId = intval(Request::Input('id', 0));
        $mobile  = filterVar(Request::Input('mobile', ''));
        $username      = filterVar(Request::Input('username', ''));
        $name          = filterVar(Request::Input('name', ''));
        $password1     = filterVar(Request::Input('password', ''));
        $password2     = filterVar(Request::Input('password_confirmation', ''));

        // UserId
        $userId            = intval(Request::Input('id', 0));
        if (!$userId) {
            $userCout = User::where('mobile', $mobile)->count();
            if ($userCout) {
                return '该手机号码已经注册';
            }
        }

        if (!checkMobile($mobile)) {
            return '手机号码格式不正确';
        }
        // 用户名验证
        if ($username) {
            if ($userId) {
                $userCount = User::where('username', $username)->where('status', 1)->where('id', '!=', $userId)->count();
            } else {
                $userCount = User::where('username', $username)->where('status', 1)->count();
            }
            if ($userCount) {
                return '用户名已经存在';
            }
        }
        // 昵称验证
        if ($name) {
            if ($userId) {
                $userCount = User::where('name', $name)->where('status', 1)->where('id', '!=', $userId)->count();
            } else {
                $userCount = User::where('name', $name)->where('status', 1)->count();
            }
            if ($userCount) {
                return '昵称已经存在';
            }
        }
        
        if (!$userId && (!$password1 || !$password2)) {
            return '密码不能为空';
        }
        if ($password1 != $password2) {
            return '两次输入密码不一致';
        }
        return 'ok';

    }

    /**
     * 通用保存
     */
    private function save()
    {
        $mobile       = filterVar(Request::Input('mobile', ''));
        $username     = filterVar(Request::Input('username', ''));
        $name         = filterVar(Request::Input('name', ''));
        $password     = filterVar(Request::Input('password', ''));
        $gender       = intval(Request::Input('gender', 0));
        $role         = intval(Request::Input('role', 0));
        $group        = intval(Request::Input('group', 0));
        $enabled      = intval(Request::Input('enabled', 0));

        // 获取用户ID
        $userId = intval(Request::Input('id', 0));

        $userData = array(
            'mobile'     => $mobile,
            'username'   => $username,
            'name'       => $name,
            'gender'     => $gender,
            'role'       => $role,
            'group'      => $group,
            'enabled'    => $enabled,
        );
        // 需要修改密码
        if ($userId && $password) {
            $userData['password'] = generatePassword($password);
        } else if (!$userId) {
            $userData['password'] = generatePassword($password);
        }
        if ($userId) {
            User::where('id', $userId)->update($userData);
        } else {
            $userData['created_at'] = date('Y-m-d H:i:s');
            return User::insertGetId($userData);
        }
    }

    public function postDelete()
    {
        $id = intval(Request::Input('id', 0));
        if ($id) {
            User::where('id', $id)->update(array('status' => User::STATUS_DELETED));
        }
        return response()->json(['error_code' => 0, 'error_desc' => '操作成功']);
    }

}
