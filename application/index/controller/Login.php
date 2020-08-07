<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/8/3
 * Time: 15:34
 */
namespace app\index\controller;

use app\common\controller\Backend;
use app\index\model\User;
use think\Controller;
use think\Db;
use think\Validate;

class Login extends Controller{
    /**
     * @Notes
     * @Author XXX
     * @DateTime DateTime
     * @return mixed
     */
    public function login(){
        return $this->view->fetch();
    }
    public function register(){
        return $this->view->fetch();
    }
    /**
     * @Notes 太阳百货登录
     * @Author XXX
     * @DateTime 2020/8/7 14:22
     */
    public function dologin(){
        $username = $this->request->post('username');
        $password = $this->request->post('pwd');
        $where = ['username'=>$username,'password'=>$password];
        $userInfo = Db::table('think_user')->where($where)->find();
        $rule = [
            '用户名'     => 'require|length:3,30',
            '密码'       => 'require|length:3,30',
        ];
        $data = [
            '用户名'     => $username,
            '密码'       => $password,
        ];
        $validate = new Validate($rule, [], ['username' => $username, 'password' => $password]);
        $result = $validate->check($data);
        if (!$result) {
            $this->error($validate->getError());
        }
        if($userInfo){
            $this->success('登录成功','index/index');
        }else{
            $this->error('账号或者密码不对');
        }
    }
    /**
     * @Notes 太阳百货注册
     * @Author XXX
     * @DateTime 2020/8/6 14:20
     */
    public function doregister(){
        $ids = $this->request->post("id");
        $username = $this->request->post('username');
        $pwd1 = $this->request->post('pwd1');
        $pwd2 = $this->request->post('pwd2');
        $loginInfo = Db::table('think_user')->column('username');
        $rule = [
            '用户名'          => 'require|length:3,30',
            '密码'            => 'require|length:3,30',
            '再次输入密码'    => 'require|length:3,30',
        ];
        $data = [
            '用户名'          => $username,
            '密码'            => $pwd1,
            '再次输入密码'    => $pwd2,
        ];
        $validate = new Validate($rule, [], ['username' => $username, 'password' => $pwd1]);
        $result = $validate->check($data);
        if (!$result) {
            $this->error($validate->getError());
        }
        if($pwd1 != $pwd2){
            $this->error('两次密码输入不一样，请重新输入');
        }else if(in_array($username,$loginInfo)){
            $this->error('用户名已存在，请重新注册');
        }
        $data = ['username'=>$username,'password'=>$pwd1,];
        Db::table("think_user")->where('id',$ids)->insert($data);
        $this->success('注册成功','login/login');
    }
}