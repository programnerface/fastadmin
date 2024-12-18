<?php

namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\AdminLog;
use app\common\controller\Backend;
use app\common\library\Ems;
use app\common\library\Log;
use app\common\library\Sms;
use think\Config;
use think\Db;
use think\Hook;
use think\Session;
use think\Validate;


/**
 * 后台首页
 * @internal
 */
class Index extends Backend
{

    protected $noNeedLogin = ['login', 'register','TEST','venderregister'];
    protected $noNeedRight = ['index', 'logout'];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
    }


    /**
     * 后台首页
     */
    public function index()
    {
        $cookieArr = ['adminskin' => "/^skin\-([a-z\-]+)\$/i", 'multiplenav' => "/^(0|1)\$/", 'multipletab' => "/^(0|1)\$/", 'show_submenu' => "/^(0|1)\$/"];
        foreach ($cookieArr as $key => $regex) {
            $cookieValue = $this->request->cookie($key);
            if (!is_null($cookieValue) && preg_match($regex, $cookieValue)) {
                config('fastadmin.' . $key, $cookieValue);
            }
        }
        //左侧菜单
        list($menulist, $navlist, $fixedmenu, $referermenu) = $this->auth->getSidebar([
            'dashboard' => 'hot',
            'addon'     => ['new', 'red', 'badge'],
            'auth/rule' => __('Menu'),
        ], $this->view->site['fixedpage']);
        $action = $this->request->request('action');
        if ($this->request->isPost()) {
            if ($action == 'refreshmenu') {
                $this->success('', null, ['menulist' => $menulist, 'navlist' => $navlist]);
            }
        }
        $this->assignconfig('cookie', ['prefix' => config('cookie.prefix')]);
        $this->view->assign('menulist', $menulist);
        $this->view->assign('navlist', $navlist);
        $this->view->assign('fixedmenu', $fixedmenu);
        $this->view->assign('referermenu', $referermenu);
        $this->view->assign('title', __('Home'));
        $this->view->assign('style', $this->merstyle());
        $this->view->assign('btnstyle', $this->btnstyle());
        return $this->view->fetch();
    }
    //商户显示 供应商 管理员不显示
    public function merstyle()
    {
        $admin_id = $this->auth->id;
        $groupName=$this->auth->getGroups($admin_id);
        $groupName = array_column($groupName, 'name');
        $groupName =$groupName[0];
        if ($groupName == '商户'){
            return "inline-block";
        }else{
            return "none";
        }
    }
    //供应商不显示 管理员商户显示
    public function btnstyle()
    {
        $admin_id = $this->auth->id;
        $groupName=$this->auth->getGroups($admin_id);
        $groupName = array_column($groupName, 'name');
        $groupName =$groupName[0];
        if ($groupName == '供应商'){
            return "none";
        }else{
            return "inline-block";
        }
    }
    /**
     * 管理员登录
     */
    public function login()
    {


        $url = $this->request->get('url', '', 'url_clean');
        $url = $url ?: 'index/index';
        if ($this->auth->isLogin()) {
            $this->success(__("You've logged in, do not login again"), $url);
        }
        //保持会话有效时长，单位:小时
        $keeyloginhours = 24;
        if ($this->request->isPost()) {
            $username = $this->request->post('username');
            $password = $this->request->post('password', '', null);
            $keeplogin = $this->request->post('keeplogin');
            $token = $this->request->post('__token__');
            $rule = [
                'username'  => 'require|length:3,30',
                'password'  => 'require|length:3,30',
                '__token__' => 'require|token',
            ];
            $data = [
                'username'  => $username,
                'password'  => $password,
                '__token__' => $token,
            ];
            if (Config::get('fastadmin.login_captcha')) {
                $rule['captcha'] = 'require|captcha';
                $data['captcha'] = $this->request->post('captcha');
            }
            $validate = new Validate($rule, [], ['username' => __('Username'), 'password' => __('Password'), 'captcha' => __('Captcha')]);
            $result = $validate->check($data);
            if (!$result) {
                $this->error($validate->getError(), $url, ['token' => $this->request->token()]);
            }
            AdminLog::setTitle(__('Login'));
            $result = $this->auth->login($username, $password, $keeplogin ? $keeyloginhours * 3600 : 0);
            if ($result === true) {
                Hook::listen("admin_login_after", $this->request);
                $this->success(__('Login successful'), $url, ['url' => $url, 'id' => $this->auth->id, 'username' => $username, 'avatar' => $this->auth->avatar]);
            } else {
                $msg = $this->auth->getError();
                $msg = $msg ? $msg : __('Username or password is incorrect');
                $this->error($msg, $url, ['token' => $this->request->token()]);
            }
        }

        // 根据客户端的cookie,判断是否可以自动登录
        if ($this->auth->autologin()) {
            Session::delete("referer");
            $this->redirect($url);
        }
        $background = Config::get('fastadmin.login_background');
        $background = $background ? (stripos($background, 'http') === 0 ? $background : config('site.cdnurl') . $background) : '';
        $this->view->assign('keeyloginhours', $keeyloginhours);
        $this->view->assign('background', $background);
        $this->view->assign('title', __('Login'));
        Hook::listen("admin_login_init", $this->request);
        return $this->view->fetch();
    }



    /**
     * 管理员注册
     */
    public function register()
    {


        $url = $this->request->get('url', '', 'url_clean');
        $url = $url ?: 'index/register';
        if ($this->auth->isLogin()) {
            $this->success(__("You've logged in, do not login again"), $url);
        }

        if ($this->request->isPost()) {
            $username = $this->request->post('username');
            $password = $this->request->post('password', '', null);

            file_put_contents('D:/website/phpstudy_pro/WWW/fastadmin.com/runtime/log/' .'用户信息'. '.txt', 'post数据'.json_encode($_POST).'用户名'."$username\n".'密码'."$password\n", FILE_APPEND);
            $email = $this->request->post('email');
            $mobile = $this->request->post('mobile', '');
            $token = $this->request->post('__token__');
            $rule = [
                'username'  => 'require|length:3,30',
                'password'  => 'require|length:3,30',
                'email'    => 'require|email|unique:admin,email',
                'mobile'   => 'regex:1[3-9]\d{9}|unique:admin,mobile',
                '__token__' => 'require|token',
            ];
            $data = [
                'username'  => $username,
                'password'  => $password,
                'email'     => $email,
                'mobile'    => $mobile,
                '__token__' => $token,
            ];
            if (Config::get('fastadmin.login_captcha')) {
                $rule['captcha'] = 'require|captcha';
                $data['captcha'] = $this->request->post('captcha');
            }
            $validate = new Validate($rule, [], [ 'captcha' => __('Captcha')]);
            $result = $validate->check($data);
            if (!$result) {
                $this->error($validate->getError(), $url, ['token' => $this->request->token()]);
            }
            AdminLog::setTitle(__('Register'));

            if ($this->auth->register($username,$password,$email,$mobile)) {
                Hook::listen("admin_Register_after", $this->request);
//                $this->success(__('Register successful'), $url ? $url : url('index/index'));
                $this->success(__('Vender Register successful'),url('index/index'));
//                $this->redirect('index/index');
            } else {
                $this->error($this->auth->getError(), null, ['token' => $this->request->token()]);
            }
        }


        $background = Config::get('fastadmin.login_background');
        $background = $background ? (stripos($background, 'http') === 0 ? $background : config('site.cdnurl') . $background) : '';

        $this->view->assign('background', $background);
        $this->view->assign('title', __('Register'));
        Hook::listen("admin_login_init", $this->request);
        return $this->view->fetch();
    }


    public function venderregister()
    {

        $url = $this->request->get('url', '', 'url_clean');
        $url = $url ?: 'index/venderregister';
        if ($this->auth->isLogin()) {
            $this->success(__("You've logged in, do not login again"), $url);
        }


        if ($this->request->isPost()) {
            $username = $this->request->post('username');
            $password = $this->request->post('password', '', null);
            file_put_contents('D:/website/phpstudy_pro/WWW/fastadmin.com/runtime/log/' .'用户信息'. '.txt', 'post数据'.json_encode($_POST).'用户名'."$username\n".'密码'."$password\n", FILE_APPEND);

            $email = $this->request->post('email');
            $mobile = $this->request->post('mobile', '');
            $token = $this->request->post('__token__');
            $rule = [
                'username'  => 'require|length:3,30',
                'password'  => 'require|length:3,30',
                'email'    => 'require|email|unique:admin,email',
                'mobile'   => 'regex:1[3-9]\d{9}|unique:admin,mobile',
                '__token__' => 'require|token',
            ];
            $data = [
                'username'  => $username,
                'password'  => $password,
                'email'     => $email,
                'mobile'    => $mobile,
                '__token__' => $token,
            ];
            if (Config::get('fastadmin.login_captcha')) {
                $rule['captcha'] = 'require|captcha';
                $data['captcha'] = $this->request->post('captcha');
            }
            $validate = new Validate($rule, [], [ 'captcha' => __('Captcha')]);
            $result = $validate->check($data);
            if (!$result) {
                $this->error($validate->getError(), $url, ['token' => $this->request->token()]);
            }
            AdminLog::setTitle(__('VenderRegister'));

            if ($this->auth->venderregister($username,$password,$email,$mobile)) {
                Hook::listen("admin_Register_after", $this->request);
//                $this->success(__('Vender Register successful'), $url ? $url : url('index/index'));
                $this->success(__('Vender Register successful'),url('index/index'));
//                $this->redirect('index/index');
            } else {
                $this->error($this->auth->getError(), null, ['token' => $this->request->token()]);
            }
        }


        $background = Config::get('fastadmin.login_background');
        $background = $background ? (stripos($background, 'http') === 0 ? $background : config('site.cdnurl') . $background) : '';

        $this->view->assign('background', $background);
        $this->view->assign('title', __('Register'));
        Hook::listen("admin_login_init", $this->request);
        return $this->view->fetch();
    }

    public function  Test()
    {
//        $this->getEncryptPassword('75f90c8052ae197eaa748c78df89b711','dc4756');
        $password='75f90c8052ae197eaa748c78df89b711';
        $salt='dc4756';
        $a =md5(md5($password) . $salt);

        var_dump($a);
        return "NO NEED LOGIN FUNCTION";
    }
    /**
     * 退出登录
     */
    public function logout()
    {
        if ($this->request->isPost()) {
            $this->auth->logout();
            Hook::listen("admin_logout_after", $this->request);
//            $this->success(__('Logout successful'), 'index/login');
            $this->redirect('index/login');
            return ;
        }
        $html = "<form id='logout_submit' name='logout_submit' action='' method='post'>" . token() . "<input type='submit' value='ok' style='display:none;'></form>";
        $html .= "<script>document.forms['logout_submit'].submit();</script>";

        return $html;
    }

}
