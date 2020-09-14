<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\SoftCate as CateModel;
use think\Session;

class Common extends Controller
{
    protected $user = false;
    protected $setting;
    public function _initialize(){
        $url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if(strstr($url,'list')){
            $current = 2;
        }else{
            $current = 1;
        }
        $this->assign('current',$current);
        if(isset($_COOKIE['session_id'])){
            session_id($_COOKIE['session_id']);
            session('n','');
            if(isset($_SESSION['user_id'])){
                session('user_id',$_SESSION['user_id']);
            }
            if(isset($_SESSION['username'])){
                session('username',$_SESSION['username']);
            }
            if(isset($_SESSION['userpic'])){
                session('userpic',$_SESSION['userpic']);
            }
        }

        //网站设置全局变量
        // private function get_setting() {
        // if (S('setting')) {
        //     $set = S('setting');
        // } else {
        //     $setting_mod = D('conf');
        //     $setting = $setting_mod -> select();
        //     foreach ($setting as $val) {
        //         $set[$val['enname']] = $val['value'];
        //     } 
        // } 
        // $this -> setting = $set;
        // $this -> assign('setting', $this -> setting);
        // } 


    	//当前位置
        if(input('cateid')){
            $this->getPos(input('cateid'));
        }
        if(input('artid')){
            $articles=db('article')->field('cateid')->find(input('artid'));
            $cateid=$articles['cateid'];
            $this->getPos($cateid);
        }
        //网站title
        $titleConf = db('conf')->where(['cnname'=>'新闻板块站点名称'])->value('value');
        $this->assign('titleConf',$titleConf);
        //网站配置项
    	$this->getConf();
        //网站栏目导航
        $this->getNavCates();
        $nav = CateModel::NavCate();
        //底部导航信息
        $cateM=new \app\index\model\Cate();
        $recBottom=$cateM->getRecBottom();
        $this->assign([
            'nav'=>$nav
        ]);
//        $user_id = cookie('user_id');
//        if($user_id){
//            session('user_id',$user_id);
//            session('username',cookie('username'));
//        }
        $this->assign('recBottom',$recBottom);
    }

    public function getNavCates(){
        $cateres=db('cate')->where(array('pid'=>0))->select();
        foreach ($cateres as $k => $v) {
            $children=db('cate')->where(array('pid'=>$v['id']))->select();
            if($children){
                $cateres[$k]['children']=$children;
            }else{
                $cateres[$k]['children']=0;
            }
        }
        $this->assign('cateres',$cateres);
    }

    public function getConf(){
        $conf=new \app\index\model\Conf();
        $_confres=$conf->getAllConf();
        $confres=array();
        foreach ($_confres as $k => $v) {
            $confres[$v['enname']]=$v['cnname'];
        }
        $this->assign('confres',$confres);
    }

    public function getPos($cateid){
        $cate= new \app\index\model\Cate();
        $posArr=$cate->getparents($cateid);
        // dump($posArr); die;
        $this->assign('posArr',$posArr);
    }


}
