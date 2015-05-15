<?php namespace App\Models;

use Cache;
use Config;
use Http;
use SaeTClientV2;
use App\Models\SNSAccount;
use Illuminate\Database\Eloquent\Model;

class User extends Model {

    protected $table = 'user';

    protected $guarded = array();

    protected $hidden = array('password');

    protected $timestamp = false;

    public static $error_code = 0;

    const STATUS_OK       = 1;   // 正常
    const STATUS_DELETED  = 0;   // 删除

    const ENABLED_TRUE    = 1;   // 帐户启用
    const ENABLED_FALSE   = 0;   // 帐户停用

    const GENDER_SECRET   = 0;  // 性别：保密
    const GENDER_MALE     = 1;  // 性别：男
    const GENDER_FEMALE   = 2;  // 性别：女

    const ROLE_USER       = 1;  // 普通用户
    const ROLE_ADMIN      = 99; // 管理员

    const GROUP_NORMAL      = 0;
    const GROUP_STAFF       = 1;
    const GROUP_SUPERVISOR  = 2;

    const VERIFYCODE_TYPE_FINDPASSWORD      = 0;    // 找回密码
    const VERIFYCODE_TYPE_REGISTR           = 1;    // 注册
    const VERIFYCODE_TYPE_BIND_MOBILE_PHONE = 2;    // 绑定手机号

    const VERIFIED_TRUE    = 1; //已认证
    const VERIFIED_FALSE   = 0; //未认证

    public static $genderList = [
        self::GENDER_SECRET => '保密',
        self::GENDER_MALE   => '男',
        self::GENDER_FEMALE => '女',
    ];

    // 认证
    public static $verifiedList = [
        self::VERIFIED_TRUE  => '是',
        self::VERIFIED_FALSE => '否',
    ];


    /**
     * 验证用户组
     */
    public static function validGroup($groupId = 0)
    {
        if (in_array($groupId, [self::GROUP_NORMAL, self::GROUP_STAFF, self::GROUP_SUPERVISOR])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证性别
     */
    public static function validGender($gender)
    {
        if (in_array($gender, [self::GENDER_SECRET, self::GENDER_MALE, self::GENDER_FEMALE])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检测验证码类型
     *
     * @param int $verifycodeType
     *
     * @return boolean
     */
    public static function checkVerifycodeType($verifycodeType = -1)
    {
        if (in_array($verifycodeType, array(self::VERIFYCODE_TYPE_REGISTR, self::VERIFYCODE_TYPE_FINDPASSWORD, self::VERIFYCODE_TYPE_BIND_MOBILE_PHONE))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证昵称
     * @param string $name
     */
    public static function validName($name = '')
    {
        return User::where('name', $name)->count();
    }

    /**
     * 验证手机
     * @param string $mobile
     */
    public static function validMobile($mobile = '')
    {
        return User::where('mobile', $mobile)->count();
    }

    /**
     * 验证手机号码
     * @param string $mobile     手机号
     * @param string $verifycode       验证码
     * @param number $verifycode_type  验码码类型
     * @return string
     */
    public static function checkMobileVerifycode($mobile = '', $verifycode = '', $verifycode_type = 0)
    {
        // 验证码验证
        if (!in_array($verifycode_type, array(self::VERIFYCODE_TYPE_BIND_MOBILE_PHONE, self::VERIFYCODE_TYPE_FINDPASSWORD, self::VERIFYCODE_TYPE_REGISTR))) {
            self::$error_code = 1302;
            return '验证码类型错误';
        }

        if (!$verifycode) {
            self::$error_code = 1302;
            return '请输入验证码';
        }

        $cacheVerifycode = Cache::get('verifycode_'.$verifycode_type.'_'.$mobile, '');

        if (strcasecmp($verifycode, $cacheVerifycode) != 0) {
            // 验证码无效
            self::$error_code = 1302;
            return '验证码不正确';
        }

        return 'ok';
    }

    /**
     * 用户头像
     */
    public function avatar()
    {
        if ($this->avatar) {
            return $this->avatar;
        } else {
            return url('backend/images/empty_avatar.png');
        }
    }

    /**
     * 状态转换为html
     *
     * @return string
     */
    public function statusToHtml()
    {
        if ($this->enabled == self::ENABLED_FALSE) {
            $class = 'badge-danger';
            $text = '停用';
        } else {
            switch ($this->status) {
            case self::STATUS_OK:
                $class = 'badge-success';
                $text = '正常';
                break;
            default:
                $class = 'badge-danger';
                $text = '已删除';
                break;
            }
        }

        return "<span class=\"badge $class\">$text</span>";
    }

    /**
     * 格式化API
     */
    public function formatToApi($userInfo = null)
    {
        if (!$userInfo) {
            $userInfo = $this;
        }
        return [
            'id'          => $userInfo->id,
            'mobile'      => $userInfo->mobile,
            'bind_mobile' => $userInfo->mobile?1:0,
            'name'        => $userInfo->name,
            'group'       => $userInfo->group,
            'gender'      => intval($userInfo->gender),
            'age'         => $userInfo->age,
            'avatar'      => apiImage($userInfo->avatar),
            'joined_at'   => apiTime($userInfo->created_at)
        ];
    }

    /**
     * 检测用户的状态
     */
    public static function checkUserStatus($userInfo = null)
    {
        if (!$userInfo) {
            return '用户不存在';
        }
        if ($userInfo->enabled == self::ENABLED_FALSE) {
            return '用户被停用';
        }
        if ($userInfo->status == self::STATUS_DELETED) {
            return '用户已经删除';
        }
        return 'ok';
    }

    /**
     * 获取用户SNS资料
     *
     * @param string  $platform    平台名称
     * @param integer $snsUserId   平台用户ID
     * @param string  $accessToken access token
     * @param string  $authKey     QQ用的auth key
     * @param string  $expires     期间时间
     *
     * @return User
     */
    public static function getSNSUser($vendor, $snsUserId, $accessToken, $authKey, $expires)
    {
        $snsConfig = Config::get('3rdkey');
        $snsUserInfo = [];

        switch ($vendor) {
            case SNSAccount::VENDOR_WEIBO:
                $cfg = $snsConfig['weibo'];
                $snsUser = self::getSinaUser($cfg['app_id'], $cfg['app_key'], $snsUserId, $accessToken);
                if (!$snsUser) {
                    return false;
                }
                $snsUserInfo['nickname']   = $snsUser['screen_name'];
                $snsUserInfo['gender']     = $snsUser['gender'] == 'm' ? 1 : 0;
                $snsUserInfo['avatar_url'] = $snsUser['avatar_large'];
                break;

            case SNSAccount::VENDOR_WEIXIN:
                $snsUser = self::getWeixinUser($snsUserId, $accessToken);
                if ($snsUser['errcode'] != '0') {
                    return false;
                }
                $snsUserInfo['nickname']   = $snsUser['data']['nickname'];
                $snsUserInfo['gender']     = $snsUser['data']['gender'];
                $snsUserInfo['avatar_url'] = $snsUser['data']['avatar_url'];
                break;

            case SNSAccount::VENDOR_QQ:
                $cfg = $snsConfig['qq'];
                $snsUser = self::getQQUserInfo($cfg['app_id'], $snsUserId, $accessToken);
                if ($snsUser['errcode'] != '0') {
                    return false;
                }
                $snsUserInfo['nickname']   = $snsUser['data']['nickname'];
                $snsUserInfo['gender']     = $snsUser['data']['gender'];
                $snsUserInfo['avatar_url'] = $snsUser['data']['avatar_url'];
                break;
            default:
                return false;
        }

        $r = SNSAccount::where('sns_id', $snsUserId)->first();
        //如果登录过并且用户存在
        if ($account = SNSAccount::where('sns_id', $snsUserId)->first()) {
            //如果存在就返回已经存在的用户
            if ($account->user) {
                return $account->user;
            }
        }

        $user = array(
                'name'       => $snsUserInfo['nickname'],
                'username'   => $snsUserInfo['nickname'] . substr($snsUserId, 3, 3) . mt_rand(0,100),
                'password'   => generatePassword($snsUserInfo['nickname'].$snsUserId),
                'gender'     => $snsUserInfo['gender'],
                'avatar'     => $snsUserInfo['avatar_url'],
        );
        $repeat = false;
        // 检查昵称重复
        if (User::where('name', $user['name'])->first()) {
            $user['name'] = $user['name'];
            $repeat = true;
        }

        \Log::info($user);
        $user = User::firstOrCreate($user);
        // 如果SNS账户已经绑定过但是用户表中不存在（这情况基本不会出现，除非手动去用户表删除了）
        if (!$account) {
            $account = new SNSAccount();
            $account->vendor       = $vendor;
            $account->sns_id       = $snsUserId;
            $account->auth_key     = $authKey;
            $account->auth_token   = $accessToken;
            $account->expired_time = $expires;
            $account->user_id      = $user->id;
            $account->save();
        } else {
            $account->user_id = $user->id;
        }

        $account->save();

        $user->nickname_repeat = $repeat;//用于给API反馈昵称重复状态

        return $user;
    }

    /**
     * 获取新浪用户资料
     *
     * @return array
     */
    public static function getSinaUser($appId, $appKey, $snsUserId, $accessToken)
    {
        $client = new SaeTClientV2($appId, $appKey, $accessToken);
        $user = $client->show_user_by_id($snsUserId);

        $res = json_decode($user, true);
        $errorLog = "/tmp/sina_error/".date('Y-m-d').'.log';
        if (!is_dir(dirname($errorLog))) {
            mkdir(dirname($errorLog));
        }
        //var_dump($res);
        if (empty($res['id'])) {
            error_log("\n[".date('Y-m-d H:i:s')."]error: ".var_export($user, true)."，userid:$snsUserId, accessToken:$accessToken",3,$errorLog);
            return false;
        }
        return $res;
    }

    /**
     * 获取微信用户信息
     * @param string $openid
     * @param string $access_token
     * @return $snsUser
     */
    public static function getWeixinUser($openid = '', $access_token = '')
    {
        $weixinUserUrl = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}";
        $r = Http::request($weixinUserUrl);
        $weixinUserInfo = json_decode($r, true);
        if ($weixinUserInfo && isset($weixinUserInfo['nickname'])) {
            $snsUser['errcode'] = 0;
            $snsUser['data']['nickname']   = $weixinUserInfo['nickname'];
            $snsUser['data']['gender']     = $weixinUserInfo['sex'];
            $snsUser['data']['avatar_url'] = $weixinUserInfo['headimgurl'];
        } else {
            $snsUser['errcode'] = 1;
        }
        return $snsUser;
    }

    /**
     * 获取QQ用户信息
     */
    public static function getQQUserInfo($appKey = '', $openId = '', $accessToken = '')
    {
        $qqInfoUrl = "https://graph.qq.com/user/get_user_info?oauth_consumer_key={$appKey}&access_token={$accessToken}&openid={$openId}&format=json";
        \Log::info($qqInfoUrl);
        $r = Http::request($qqInfoUrl);
        $qqInfo = json_decode($r, true);

        $snsUser = [];
        if ($qqInfo && isset($qqInfo['nickname'])) {
            $snsUser['errcode'] = 0;
            $snsUser['data']['nickname']   = $qqInfo['nickname'];
            if ($qqInfo['gender'] == '男') {
                $snsUser['data']['gender'] = 1;
            } else if ($qqInfo['gender'] == '女'){
                $snsUser['data']['gender'] = 2;
            } else {
                $snsUser['data']['gender'] = 0;
            }
            if (isset($qqInfo['figureurl_qq_2'])) {
                $snsUser['data']['avatar_url'] = $qqInfo['figureurl_qq_2'];
            } else {
                $snsUser['data']['avatar_url'] = $qqInfo['figureurl_qq_1'];
            }
        } else {
            $snsUser['errcode'] = 1;
        }

        return $snsUser;
    }

    /**
     * 用户相关的设备
     *
     * @return object
     */
    public function client()
    {
        return $this->hasOne('App\Models\ClientInfo', 'user_id');
    }
}
