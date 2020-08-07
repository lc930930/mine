<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/8/3
 * Time: 16:00
 */
namespace app\index\model;

use think\Db;
use think\Model;

class User extends Model{
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    static public function getAdmin()
    {
        $admin = Db::table('think_user')->select();
        return $admin;
    }

}