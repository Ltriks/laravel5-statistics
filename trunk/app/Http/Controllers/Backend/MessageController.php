<?php namespace App\Http\Controllers\Backend;


use View;
use Redirect;
use Validator;
use App\Http\Requests;
use App\Models\Message;
use App\Http\Controllers\Controller;


use Request;

class MessageController extends BaseController {

    /**
     * 列表
     *
     * @return Response
     */
    public function getAll()
    {
        if (!empty($sort = Request::Input('sort'))) {
            list($sortField, $by) = explode('-', $sort);
        } else {
            $sortField = 'id';
            $by = 'DESC';
        }
        $messages = Message::orderBy($sortField, $by)->where(function($query){
            //关键字搜索
            if (!empty($keyword = Request::Input('keyword'))) {
                $query->where('content', 'like', "%{$keyword}%");
            }
        })->paginate(15);

        return View::make('backend.pages.message-all')->withMessages($messages);
    }

    /**
     * 创建
     *
     * @return Response
     */
    public function getNew()
    {
        return View::make('backend.pages.message-new');
    }

    /**
     * 创建
     *
     * @return Response
     */
    public function postCreate(Request $request)
    {
        // $validator = Validator::make(Request::all(), [
        //     'message'    => 'required|min:1|max:40',
        //     'action'     => 'required|in:0,1,2,3,4,5',
        //     'object'     => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return Redirect::back()->withErrors($validator->errors)->withInput();
        // }

        $error = '';
        switch (Request::Input('action')) {

            case Message::ACTION_UNKNOWN:
                
                break;

            case Message::ACTION_TEXT:

                break;

            case Message::ACTION_LINK:
                if (!filter_var(Request::Input('object'), FILTER_VALIDATE_URL)) {
                    $error = '链接不正确';
                }
                break;
        }

        if ($error) {
            return Redirect::back()->withMessage($error)->withColor('danger')->withInput();
        }

        $message = new Message;

        $targetUser = array();
        foreach(explode(',', Request::Input('target_user', '')) as $item) {
            if(!empty($item)) $targetUser[] = trim($item);
        }

        //,1,2,3,
        if (count($targetUser)) {
            $targetUser = ','. join(',', $targetUser) . ',';
        } else {
            $targetUser = '';
        }

        $message->content     = strip_tags(Request::Input('message'));
        $message->action      = Request::Input('action');
        $message->object      = Request::Input('object');
        $message->type        = Request::Input('type', 1);
        $message->target_user = $targetUser;

        $message->save();

        return Redirect::back()->withMessage('创建成功！');
    }

    /**
     * 推送一条消息
     *
     * @param integer $id
     *
     * @return Response
     */
    public function getPush($id)
    {
        // 再次推送消息
        // AdminLog::log($id, '再次推送消息');

        pushMessage($id);
        Message::whereId($id)->update(array('status' => Message::STATUS_OK));

        return Redirect::back()->withMessage('已推送！');
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

        Message::whereIn('id', $id)->delete();

        // AdminLog::log($id, '删除消息');

        return Redirect::back()->withMessage('删除成功！');
    }
}