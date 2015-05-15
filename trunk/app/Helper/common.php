<?php

use \Carbon\Carbon;
use App\Models\Message;
use App\Models\ClientInfo;

/**
 * 字符串过虑
 *
 * @param string $var
 *
 * @return string
 */
function filterVar($var)
{
    if (is_string($var)) {
        return htmlspecialchars($var);
    }
    return $var;
}

/**
 * 检测用户是否有某一个权限
 *
 * @param string $permission
 *
 * @return boolean
 */
function UserHasPermission($permission)
{
    $groupPermission = getGroupPermission(getUserInfo('group'));
    if ($groupPermission === '*') {
        return true;
    }

    return str_is("*{$permission}*", $groupPermission);
}

/**
 * 获取权限组的权限
 *
 * @param integer $groupId
 *
 * @return string
 */
function getGroupPermission($groupId)
{
    if (!$groupId) {
        return '';
    }
    $cacheKey = sprintf(Config::get('cache.keys.user_group_perm'), $groupId);

    if (empty($permission = Cache::get($cacheKey, ''))) {
        $permission = App\Models\UserGroup::findOrFail($groupId)->permission;
        Cache::put($cacheKey, $permission, 5);
    }

    return $permission;
}

/**
 * 检查当前url是否属于菜单中定义的模式，完成菜单激活状态
 *
 * @param array $pattern app/config/menu.php中的pattern
 *
 * @return boolean
 */
function is_current_model(array $pattern)
{
    foreach ($pattern as $ptn) {
        if (Request::is($ptn)) {
            return true;
        }
    }

    return false;
}

/**
 * 验证手机号码
 *
 * @param string $mobile 手机号码
 *
 * @return boolean
 */
function checkMobile($mobile = 0)
{
    if (!$mobile) {
        return false;
    }
    if(preg_match('/1[34578]{1}\d{9}/', $mobile)){
        return true;
    } else {
        return false;
    }
}

/**
 * 获取文件拓展名
 *
 * @param string $filename
 *
 * @return string
 */
function fileExt($filename)
{
    $ext = pathinfo($filename, PATHINFO_EXTENSION);

    return empty($ext) ? '' : ".$ext";
}

function getUploadPath($type = "avatar")
{
    return public_path().'/static/upload/'.$type.'/';
}

/********************************************* 用户相关 start *************************************************/
/**
 * 生成密码
 * @param unknown $password
 * @return string
 */
function generatePassword($password)
{
    $str = '802lQmdTwXRv6x6W';
    return md5(md5($password.$str));
}

/**
 * 将用户信息存入session
 * @param number $userId
 * @param string $value
 */
function sessionUserInfo($value = '')
{
    Session::put('user_info', $value, 3600);
}

/**
 * 登录session验证
 */
function checkLoginSession()
{
    return Session::get('user_info', array());
}

/**
 * 获取用户登录信息
 * @param string $fields
 * @param string $default
 * @return unknown|string
 */
function getUserInfo($fields = '', $default = '')
{
     $userInfo = Session::get('user_info', array());
     if (isset($userInfo[$fields])) {
         return $userInfo[$fields];
     } else {
         return $default;
     }
}

/**
 * 是否是管理员登录
 * @return boolean
 */
function isAdminLogin()
{
    $userInfo = Session::get('user_info', array());
    if (isset($userInfo['role']) && $userInfo['role'] == App\Models\User::ROLE_ADMIN) {
        return true;
    } else {
        return false;
    }
}
/********************************************* 用户相关 end ***************************************************/

/********************************************* API 相关 start ************************************************/

/**
 * 写入api日志
 *
 * @param string $content
 *
 * @return boolean
 */
function apiLog($content = '')
{
    if (!$content) {
        return false;
    }

    if (is_array($content)) {
        $content = json_encode($content);
    }
    $content .= "\n";

    $apiLogName = date('Y_m_d').'.log';
    $apiLogPath = storage_path().'/logs/api/';
    if (!is_dir($apiLogPath)) {
        @mkdir($apiLogPath);
    }
    $apiLogFilePath = $apiLogPath.$apiLogName;
    if (!file_exists($apiLogFilePath)) {
        @touch($apiLogFilePath);
    }
    if (file_exists($apiLogFilePath)) {
        error_log($content, 3, $apiLogFilePath);
        return true;
    } else {
        return false;
    }

}

/**
 * 写入日志
 *
 * @param string $content
 *
 * @return boolean
 */
function commonLog($type = '', $content = '')
{
    if (!$content) {
        return false;
    }

    if (!$type) {
        return false;
    }

    if (!is_string($content)) {
        $content = var_export($content, true);
    }
    $content .= "\n";

    $apiLogFilePath = storage_path().'/logs/'.$type.date('_Y_m_d').'.log';

    if (!file_exists($apiLogFilePath)) {
        @touch($apiLogFilePath);
    }
    if (file_exists($apiLogFilePath)) {
        error_log($content, 3, $apiLogFilePath);
        return true;
    } else {
        return false;
    }

}

/**
 * 移除数组中的null，bool转换为整型，以便于客户端JSON处理
 *
 * @param array $array
 *
 * @return array
 */
function formatRestJson($array)
{
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            if ($value === '' || is_null($value)) {
                unset($array[$key]); //移除空
            } else if (is_array($value)) {
                $value = formatRestJson($value);
                if(!empty($value)) {
                    $array[$key] = $value;
                } else {
                    unset($array[$key]);
                }
            } else if (is_bool($value)) {
                $array[$key] = intval($value); //bool转换为整型
            }
        }
    }

    return $array;
}

/**
 * 格式化图片为API格式
 *
 * @param string $image
 *
 * @return array
 */
function apiImage($image)
{
    if (empty($image)) {
        return '';
    }

    $outData = array(
            'thumb' => fullLink($image),
            'large' => fullLink($image),
    );

    $outData['width'] = 0;
    $outData['height'] = 0;

    return $outData;
}

/**
 * 补全资源链接
 *
 * @param string $res
 *
 * @return string
 */
function fullLink($res)
{
    if (empty($res) || !is_string($res)) {
        return false;
    }
    if (false !== stripos($res, 'http://') || false !== stripos($res, 'https://')) {
        return $res;
    } else {
        return url('static/upload/avatar/') . $res;
    }
}

/**
 * 转换为时间戳
 *
 * @param string $timeString
 *
 * @return integer
 */
function apiTime($timeString)
{
    return strtotime($timeString) < 0 ? '2014-01-01 00:00:00 +0800' : date('Y/m/d H:i:s O', strtotime($timeString));
}

/**
 * 短信发送
 *
 * @param
 * @return bool
 */
function sms_send($mobile, $content) {
    $flag = 0;
    $argv = array(
            'sn'=> Config::get('sms.sn'),
            'pwd'=> strtoupper(md5(Config::get('sms.sn').Config::get('sms.pwd'))),
            'mobile'=> $mobile,
            'content'=> iconv("UTF-8", "gb2312//IGNORE", Config::get('sms.sign_name').$content),
            'ext'=>'',
            'stime'=>'',
            'rrid'=>''
    );
    //http_build_query
    $params = '';
    foreach ($argv as $key=>$value) {
        if ($flag != 0) {
            $params .= "&";
            $flag = 1;
        }
        $params.= $key."="; $params.= urlencode($value);
        $flag = 1;
    }
    //
    $length = strlen($params);
    $fp = @fsockopen(Config::get('sms.host'), 8060, $errno, $errstr, 10);
    //构造post请求的头
    $header = "POST /webservice.asmx/mdSmsSend_u HTTP/1.1\r\n";
    $header .= "Host:".Config::get('sms.host')."\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: ".$length."\r\n";
    $header .= "Connection: Close\r\n\r\n";
    //添加post的字符串
    $header .= $params."\r\n";
    //发送post的数据
    fputs($fp, $header);
    $inheader = 1;
    while (!feof($fp)) {
        $line = fgets($fp,1024); //去除请求包的头只显示页面的返回数据
        if ($inheader && ($line == "\n" || $line == "\r\n")) {
            $inheader = 0;
        }
        if ($inheader == 0) {
            // echo $line;
        }
    }
    preg_match('/<string xmlns=\"http:\/\/tempuri.org\/\">(.*)<\/string>/', $line, $str);
    $result = explode("-",$str[1]);
    if(count($result)>1) {
        //echo '发送失败返回值为:'.$line."请查看webservice返回值";
        return false;
    }
    return true;
}

/**
 * 生成随机数
 *
 * @param
 * @return string
 */
function get_randStr($len=6, $format='ALL') {
    switch($format) {
        case 'ALL':
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
        case 'CHAR':
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            break;
        case 'NUMBER':
            $chars='0123456789';
            break;
        default :
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
    }
    mt_srand((double)microtime()*1000000*getmypid());
    $rand_str = "";
    while(strlen($rand_str) < $len) {
        $rand_str.= substr($chars,(mt_rand()%strlen($chars)), 1);
    }
    return $rand_str;
}

/********************************************* API end *****************************************************/

/**
 * 获取Request URI
 * @return unknown|string
 */
function getRequestUri()
{
    if (isset($_SERVER['REQUEST_URI'])) {
        return $_SERVER['REQUEST_URI'];
    } else {
        return '';
    }
}

function apiGet($key, $value = '')
{
    $r = '';

    if (isset($_POST['json'])) {
        $r = $_POST['json'];
        $r = json_decode($r, true);
    } else {

        $r = file_get_contents("php://input");
        if ($r) {
            $r = json_decode($r, true);
            $r = $r['json'];
        }
    }

    if (strstr($key, ".") === false) {
        if (isset($r[$key])) {
           return $r[$key];
        } else {
            return $value;
        }
    } else {
        $sub = explode(".", $key);
        if (isset($r[$sub[0]][$sub[1]])) {
           return $r[$sub[0]][$sub[1]];
        } else {
            return $value;
        }
    }

    
}

/**
 * 获取客户端IP地址
 *
 * @return Ambigous <string, unknown>
 */
function getClientIP()
{
    if (!empty ( $_SERVER["REMOTE_ADDR"] )) {
        $cip = $_SERVER["REMOTE_ADDR"];
    } elseif (!empty ( $_SERVER["HTTP_X_FORWARDED_FOR"] )) {
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (!empty ( $_SERVER["HTTP_CLIENT_IP"] )) {
        $cip = $_SERVER["HTTP_CLIENT_IP"];
    } else {
        $cip = "0.0.0.0";
    }
    return $cip;
}

/**
 * 验证身份证号
 */
function isCreditNo($vStr)
{
    $vCity = array(
        '11','12','13','14','15','21','22',
        '23','31','32','33','34','35','36',
        '37','41','42','43','44','45','46',
        '50','51','52','53','54','61','62',
        '63','64','65','71','81','82','91'
    );
 
    if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;
 
    if (!in_array(substr($vStr, 0, 2), $vCity)) return false;
 
    $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
    $vLength = strlen($vStr);
 
    if ($vLength == 18)
    {
        $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
    } else {
        $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
    }
 
    if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
    if ($vLength == 18)
    {
        $vSum = 0;
 
        for ($i = 17 ; $i >= 0 ; $i--)
        {
            $vSubStr = substr($vStr, 17 - $i, 1);
            $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
        }
 
        if($vSum % 11 != 1) return false;
    }
 
    return true;
}

/********************************************* 推送消息 start *************************************************/
/**
 * 推送一条消息
 *
 * @param integer $messageId
 *
 * @return boolean
 */
function pushMessage($messageId)
{
    $message = Message::findOrFail($messageId);
    $message->status = Message::STATUS_OK;//已推送
    $message->save();
    $tokens = $message->getMessageTokens();

    $date = Carbon::now()->addSeconds(5);

    if ($message->type == Message::TYPE_USER) {
        // 推个人
        if ($tokens[ClientInfo::PLATFORM_ANDROID]) {
            $aosMessage = formatAOSMessage($message, $tokens[ClientInfo::PLATFORM_ANDROID]);
            $aosMessage['type'] = Message::TYPE_USER;
            Queue::later($date, 'AOSPushWorker', $aosMessage);
        }
        if ($tokens[ClientInfo::PLATFORM_IOS]) {
            $iosMessage = formatIOSMessage($message, $tokens[ClientInfo::PLATFORM_IOS]);
            $iosMessage['type'] = Message::TYPE_USER;
            Queue::later($date, 'IOSPushWorker', $iosMessage);
        }

    } else if ($message->type == Message::TYPE_SYSTEM) {
        // 推全部
        if (!$tokens[ClientInfo::PLATFORM_IOS]) {
            $aosMessage = formatAOSMessage($message, []);
            $aosMessage['type'] = Message::TYPE_SYSTEM;
            Queue::later($date, 'AOSPushWorker', $aosMessage);
        }
        if (!$tokens[ClientInfo::PLATFORM_IOS]) {
            $iosMessage = formatIOSMessage($message, []);
            $iosMessage['type'] = Message::TYPE_SYSTEM;
            Queue::later($date, 'IOSPushWorker', $iosMessage);
        }
    }

}

/**
 * 格式化安卓消息体
 *
 * @param Message $message
 * @param array   $tokens
 *
 * @return array
 */
function formatAOSMessage(Message $message, array $tokens)
{
    $data = [
             'tokens' => $tokens,
             'message' => [
                           'description'    => $message->content,
                           'custom_content' => $message->formatToApi(),
                          ],
             'title' => Config::get('push.android.title')
            ];

    return $data;
}

/**
 * 格式化IOS消息体
 *
 * @param Message $message
 * @param array   $tokens
 *
 * @return array
 */
function formatIOSMessage(Message $message, array $tokens)
{
    $content = [
         'id'      => $message->id,
         'type'    => $message->action,
    ];
    switch ($content['type']) {
        case Message::ACTION_UNKNOWN:
            $content['unknown'] = $message->object;
            break;
        case Message::ACTION_TEXT:
            $content['text'] = $message->object;
            break;
        case Message::ACTION_LINK:
            $content['link'] = $message->object;
            break;
    }

    $data = [
        'message' => [
            'aps' => [
                'alert' => $message->content
            ],
            'lightapp_ctrl_keys' => [
                'display_in_notification_bar' => 1,
                'enter_msg_center' => 1
            ],
            'content' => $content,
        ],
        'tokens' => $tokens,
    ];

    return $data;
}


/********************************************* 推送消息 end *************************************************/
?>