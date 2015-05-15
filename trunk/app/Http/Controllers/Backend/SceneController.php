<?php namespace App\Http\Controllers\Backend;

use View;
use Redirect;
use App\Models\Scene;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;

class SceneController extends BaseController {

    public $perPage = 50;

    /**
     * 所有挂号单
     *
     * @return Response
     */
    public function getAll()
    {
        $sceneList = Scene::orderBy('id', 'DESC')->where(function($query){

        })->paginate($this->perPage);

        View::share('sceneList', $sceneList);

        return View::make('backend.pages.scene-all');
    }

    /**
     * 新建场景
     */
    public function getNew()
    {
        return View::make('backend.pages.scene-new');
    }

    /**
     * 创建场景
     */
    public function postCreate(Request $request)
    {
        $this->save();
        return Redirect::back()->withMessage('添加成功！')->withInput();
    }

    /**
     * 通用保存
     * @return number
     */
    private function save()
    {
        $id    = intval(Request::Input('id', 0));
        $title = filterVar(Request::Input('title', ''));
        $logo  = filterVar(Request::Input('logo', ''));
        $sort  = intval(Request::Input('sort', 0));

        if ($id) {
            Scene::where('id', $id)->update([
                'title' => $title,
                'logo'  => $logo,
                'sort' =>  $sort,
            ]);
        } else {
            $id = Scene::insertGetId([
                'title'      => $title,
                'logo'       => $logo,
                'sort'       =>  $sort,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $id;
    }
}
