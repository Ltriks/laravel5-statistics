<?php namespace App\Http\Controllers\Backend;

use View;
use Cache;
use Config;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class BaseController extends Controller {

    //用户权限
    protected $userPermission;

    public function __construct(Request $request)
    {
        // 这里每次都会执行
        if (!$request->is('*auth/log*') && !$request->is('*auth/authcode*')) {
            !is_null($this->userPermission) || $this->userPermission = getGroupPermission(getUserInfo('group'));
            $this->auth($request);
            View::share('menus', $this->getMenu());
        }
    }

	/**
     * 权限检查
     *
     * @return Response
     */
    public function auth($request)
    {
        //尝试从缓存读取url与权限的对照表
        if (empty($urls = Cache::get('permission_urls', array()))) {
            $urls = array();
            $permissions = Config::get('permission');
            foreach ($permissions as $module => $permission) {
                foreach ($permission['action'] as $actionName => $action) {
                    foreach ($action['urls'] as $url) {
                        $urls[$url][] = "$module.$actionName";
                    }
                }
            }
        }

        foreach ($urls as $url => $requirePermissions) {

            if ($request->is("*$url*")) {
                foreach ($requirePermissions as $perm) {

                    if (!UserHasPermission($perm)) {
                        //没有权限
                        App::abort(404);
                    }
                }
            }
        }

    }

        /**
     * 获取菜单
     *
     * @return array
     */
    public function getMenu()
    {
        $menus = Config::get('menu');
        $userPermission = $this->userPermission;
        if ($userPermission == '*') {
            return $menus;
        }
        //Log::info(!str_is("*{splash.*}",$userPermission));
        foreach ($menus as $key => $menu) {
            //如果在一级指定了permission
            if (!empty($menu['permission'])) {
                //没有这个菜单指定的权限
                if (!str_is("*{$menu['permission']}", $userPermission)) {
                    unset($menus[$key]);
                }
            } else {
                //遍历子菜单
                foreach ($menu['submenu'] as $subKey => $submenu) {
                    if (!str_is("*{$submenu['permission']}*", $userPermission)) {
                        //Log::info("*{$submenu['permission']}*");
                        unset($menus[$key]['submenu'][$subKey]);//移除
                    }
                }
                //如果全部都没满足
                if (empty($menus[$key]['submenu'])) {
                    unset($menus[$key]);
                }
            }

        }

        return $menus;
    }

	public function json($data)
	{
		return response()->json($data);
	}	

}
