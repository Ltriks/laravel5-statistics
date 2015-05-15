<?php namespace App\Http\Controllers\Backend;

use View;
use Redirect;
use App\Models\DiseaseSymptoms;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;

class DiseaseSymptomsController extends BaseController {

    public $perPage = 50;

    /**
     * 所有病症
     *
     * @return Response
     */
    public function getAll()
    {
        $diseaseSymptomsList = DiseaseSymptoms::orderBy('id', 'DESC')->where(function($query){
            $categoryId = Request::Input('category_id', 0);
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            $status = Request::Input('status', 1);
            $query->where('status', $status);

            $keyword = Request::Input('keyword', '');
            if ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            }

        })->paginate($this->perPage);

        View::share('diseaseSymptomsList', $diseaseSymptomsList);

        return View::make('backend.pages.disease-symptoms-all');
    }

    /**
     * 新建病症
     */
    public function getNew()
    {
        return View::make('backend.pages.disease-symptoms-new');
    }

    public function getEdit()
    {
        $diseaseSymptomsInfo = DiseaseSymptoms::find(Request::Input('diseaseSymptomsId', 0));

        View::share('diseaseSymptomsInfo', $diseaseSymptomsInfo);

        return View::make('backend.pages.disease-symptoms-edit');
    }

    /**
     * 创建病症
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
        $name        = filterVar(Request::Input('name', ''));
        $categoryId  = intval(Request::Input('category_id', 0));
        $sort        = intval(Request::Input('sort', 0));

        if (!$name) {
            return '名称不能为空';
        }

        if ($id) {
            $diseaseSymptomsCount = DiseaseSymptoms::where('name', $name)->where('status', DiseaseSymptoms::STATUS_OK)->where('id', '!=', $id)->count();
        } else {
            $diseaseSymptomsCount = DiseaseSymptoms::where('name', $name)->where('status', DiseaseSymptoms::STATUS_OK)->count();
        }
        if ($diseaseSymptomsCount) {
            return '名称已经存在';
        }

        if ($id) {
            DiseaseSymptoms::where('id', $id)->update([
                'category_id' => $categoryId,
                'name'        => $name,
                'sort'        =>  $sort,
            ]);
        } else {
            $id = DiseaseSymptoms::insertGetId([
                'category_id' => $categoryId,
                'name'        => $name,
                'sort'        =>  $sort,
                'created_at'  => date('Y-m-d H:i:s')
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
            DiseaseSymptoms::where('id', $id)->update(['status' => 0]);
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
            DiseaseSymptoms::where('id', $id)->update(['status' => 1]);
        }
    }
}
