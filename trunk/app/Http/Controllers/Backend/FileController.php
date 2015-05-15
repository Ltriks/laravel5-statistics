<?php namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Rest\RestBaseController;

class FileController extends BaseController
{
    public function postUpload()
    {
        if ($_FILES) {
            $fileArr = array_pop($_FILES);
            $oriName = $fileArr['name'];
            $fileExt = $this->allowUpfileType($fileArr['type']);
            if (!$fileExt) {
                return response()->json([
                    'error_code' => 1,
                    'error_desc' => '不支持文件类型',
                ]);
            } else {
                $fileName = uniqid('pic_').'.'.$fileExt;
                $avatarFile = getUploadPath('images').$fileName;
                if (move_uploaded_file($fileArr['tmp_name'], $avatarFile)) {
                    $imageUrl = url('/static/upload/images').'/'.$fileName;
                    if (file_exists($avatarFile)) {
                        return response()->json([
                            'status' => 'ok',
                            'error_code' => 0,
                            'src' => $imageUrl
                        ]);
                    }
                }
                return response()->json([
                    'error_code' => 1,
                    'error_desc' => '不支持文件类型',
                ]);
            }
        }
    }


    /**
     * 检测文件类型(并返回文件扩展名)
     *
     * @param string $type
     */
    private function allowUpfileType($type = '')
    {
        if ($type) {
            $type = strtolower($type);
            if ($type == 'image/jpeg') {
                return 'jpeg';
            } else if ($type == 'image/png') {
                return 'png';
            } else if ($type == 'image/jpg') {
                return 'jpeg';
            }
            if (in_array($type, array('image/jpeg', 'image/png', 'image/jpg'))) {
                return false;
            }
        }
        return false;
    }
}
