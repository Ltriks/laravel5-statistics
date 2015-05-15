<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model {

    protected $table      = 'user_group';
    public  $timestamps   = true;
    protected $softDelete = true;
    protected $guarded    = array();

    /**
     * 获取权限组列表
     */
    public static function getGroupList()
    {
    	return UserGroup::all();
    }
}
