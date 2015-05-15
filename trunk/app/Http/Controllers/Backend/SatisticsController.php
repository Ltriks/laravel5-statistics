<?php namespace App\Http\Controllers\Backend;

use View;
use Input;
use Session;
use Request;
use Redirect;
use Response;
use DB;
use App\Models\UserStatistics;
use App\Models\Userlogs;
use App\Models\Orderlogs;
use App\Models\UserDailyNew;
use App\Models\UserDailyNewQq;
use App\Models\UserDailyNewWeibo;
use App\Models\UserDailyNewWx;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SatisticsController extends BaseController {
    
    /**
     * 整理log，返回按照日期整理好的新增各渠道人数
     * @return [type] [description]
     */
    public function getUsernew()
    {
        $logs = $this->getReadlog(); //读取log文件
        $logdate ;  //记录时间
        $data = array();
        $count = array(
                'wx' => 0,
                'weibo' => 0,
                'qq' => 0,
                'mobile' => 0,
                 );
        foreach ($logs as $key => $value) {
            $raw = explode('|', $value);//log信息
            $logdate = date('Y-m-d', trim(strtotime($raw[0])));//log记录时间
            if (!array_key_exists($logdate , $data)) {
                $data[$logdate] = $count;
            }
            $acinfo = explode('::', $raw[4]);//action 信息
            if ((trim($acinfo[0]) == 'user') && (trim($acinfo[1]) == 'signup')) {
                // echo (trim($raw[5]));exit;
                switch (trim($raw[5])) {
                    case 'wx':
                        $data[$logdate]['wx']++;
                        break;
                    case 'qq':
                        $data[$logdate]['qq']++;
                        break;
                    case 'weibo':
                        $data[$logdate]['weibo']++;
                        break;
                    case 'mobile':
                        $data[$logdate]['mobile']++;
                        break;                   
                   default:
                       break;
                }
            }
        }

        // var_dump($data);exit;//整理之后的signup用户新增
        return $data;
    }

    /**
     * 根据Usernew返回的数据按照日期入库
     * 最关键的daily_weibo qq wx 三张表格
     * 
     */
    public function getUserstorage()
    {
        $data = $this->getUsernew();
        // var_dump($data);
        foreach ($data as $time => $sources) {
            // var_dump($value);exit;
            foreach ($sources as $source => $count) {
                $this->save($source,$time,$count);
            }
        }
        echo "over";
    }

/**
     * 通用保存
     * @return number
     */
    private function save($source,$time,$count)
    {
        // $time    = intval(Request::Input('id', 0));
        // $count  = filterVar(Request::Input('name', ''));
        if (!$time) {
            return '时间不能为空';
        }

        switch ($source) {
            case 'wx':
                $wx = UserDailyNewWx::where('time', $time)->first();
                if ($wx) {
                    //存在
                    $wx->count += $count;
                    $wx->save();
                }else{
                    $wx = new UserDailyNewWx;
                    $wx->count = $count;
                    $wx->time = $time;
                    $wx->save();
                }
                break;
            case 'weibo':
                $weibo = UserDailyNewWeibo::where('time', $time)->first();
                if ($weibo) {
                    //存在
                    $weibo->count += $count;
                    $weibo->save();
                }else{
                    $weibo = new UserDailyNewWeibo;
                    $weibo->count = $count;
                    $weibo->time = $time;
                    $weibo->save();
                }
                break;
            case 'qq':
               $qq = UserDailyNewQq::where('time', $time)->first();
                if ($qq) {
                    //存在
                    $qq->count += $count;
                    $qq->save();
                }else{
                    $qq = new UserDailyNewQq;
                    $qq->count = $count;
                    $qq->time = $time;
                    $qq->save();
                }
                break;           
        }
    }
    
    /**
     * 用户log保存
     * @return [type] [description]
     */
    public function userlogsave($userInfo)
    {
        //array(4) { [0]=> string(10) "2015-05-19" [1]=> string(4) "user" [2]=> string(3) "reg" [3]=> string(3) "qq " }
        $date   = $userInfo[0];
        $user   = $userInfo[1];
        $action = $userInfo[2];
        $source = $userInfo[3];
        $id = Userlogs::insertGetId([
                'date'      => $date,
                'user'      => $user,
                'action'    => $action,
                'source'    => $source,
            ]);
        return $id;
    }


    /**
     * 写入一行内容
     * @param  [type] $fileData [文件内容]
     * @return [type] $filepos  [文件路径]
     */
    public function getWritelog($fileData = '2015-05-19|user|reg|qq',$filepos = '../storage/logs/test.log'){
        $fp = fopen($filepos,"a"); 
        if(!$fp){ 
            echo "system error"; 
            exit(); 
        }else { 
            echo "success";
            $fileData = $fileData."\n"; 
            fwrite($fp,$fileData); 
            fclose($fp); 
        } 
    }

    /**
     * 读取文件内容
     * @param  string $filepos [文件路径]
     * @return [type]          [description]
     */
    public function getReadlog($filepos = '../storage/logs/test.log'){
        $fp = fopen($filepos,"r"); 
        while (!feof($fp)) {
            $line = fgets($fp);
            $row[] = $line;
        }
         fclose($fp); 
         return $row;
    }

    /** php 发送流文件 
    * @param  String  $url  接收的路径 
    * @param  String  $file 要发送的文件 
    * @return boolean 
    */  
    function sendStreamFile($url, $file){  
        if(file_exists($file)){  
            $opts = array(  
                'http' => array(  
                    'method' => 'POST',  
                    'header' => 'content-type:application/x-www-form-urlencoded',  
                    'content' => file_get_contents($file)  
                )  
            );  
            $context = stream_context_create($opts);  
            $response = file_get_contents($url, false, $context);  
            $ret = json_decode($response, true);  
            return $ret['success'];  
        }else{  
            return false;  
        }  
    }  
    // $ret = sendStreamFile('http://localhost/receiveStreamFile.php','send.txt');
    // var_dump($ret);  
     
    
    /** php 接收流文件 
    * @param  String  $file 接收后保存的文件名 
    * @return boolean 
    */  
    function receiveStreamFile($receiveFile){  
        $streamData = isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : '';  
      
        if(empty($streamData)){  
            $streamData = file_get_contents('php://input');  
        }  
      
        if($streamData!=''){  
            $ret = file_put_contents($receiveFile, $streamData, true);
        }else{  
            $ret = false;  
        }  
        return $ret;  
    }  

     function getTrans(){
        // $ret = $this->receiveStreamFile('','../storage/logs/test.log');
        echo "string";
     }

}
