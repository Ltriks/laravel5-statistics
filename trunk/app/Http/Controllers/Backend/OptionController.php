<?php namespace App\Http\Controllers\Backend;

use View;
use Redirect;
use App\Http\Requests;
use App\Models\Option;
use App\Http\Controllers\Controller;

use Request;

class OptionController extends BaseController {

    /**
     * 编辑积分说明
     */
    public function getEdit()
    {
        return View::make('backend.pages.option-edit');
    }

    /**
     * 保存积分说明
     */
    public function postSave()
    {
        $android_update_type = intval(Request::Input('android_update_type', 1));
        $android_alert_title = filterVar(Request::Input('android_alert_title', ''));
        $android_alert_body  = filterVar(Request::Input('android_alert_body', ''));
        $android_download    = filterVar(Request::Input('android_download', ''));

        Option::set('android_update_type', $android_update_type);
        Option::set('android_alert_title', $android_alert_title);
        Option::set('android_alert_body',  $android_alert_body);
        Option::set('android_download',  $android_download);

        $ios_update_type = intval(Request::Input('ios_update_type', 1));
        $ios_alert_title = filterVar(Request::Input('ios_alert_title', ''));
        $ios_alert_body  = filterVar(Request::Input('ios_alert_body', ''));
        $ios_download    = filterVar(Request::Input('ios_download', ''));

        Option::set('ios_update_type', $ios_update_type);
        Option::set('ios_alert_title', $ios_alert_title);
        Option::set('ios_alert_body',  $ios_alert_body);
        Option::set('ios_download',  $ios_download);

        return Redirect::back()->withMessage('更新成功！')->withInput();
    }
}