<?php
namespace app\admin\controller;
use app\admin\model\Down as DownModel;
use app\admin\model\Downclass as DownclassModel;
use app\admin\model\Ftpupload as FtpModel;
use app\admin\model\Ftpupload;
use app\admin\model\Image as ImageModel;
use app\index\model\Soft;
use app\index\model\SoftCate;
use think\Db;

class Down extends Common
{

    public function lst($keyword='',$classid='',$sclassid='',$tclassid='',$page=''){
        if($keyword){
            $where['softname'] = ['like',"%$keyword%"];
        }else{
            $where = [];
        }
        if($classid&&($sclassid==0)){
            $classids = Db::table('e_downclass')->where(['bclassid'=>$classid])->column('classid');
            $tids = Db::table('e_downclass')->where(['bclassid'=>['in',$classids]])->column('classid');
            if(!empty($classids)&&!empty($tids)){
                $classids = array_merge($classids,$tids);
            }
            $classids[] = $classid;
            $where['a.classid'] = ['in',$classids];
        }
        if($sclassid&&($tclassid==0)){
            $sclassids = Db::table('e_downclass')->where(['bclassid'=>$sclassid])->column('classid');
            $sclassids[] = $sclassid;
            $where['a.classid'] = ['in',$sclassids];
            $secondCate = Db::table('e_downclass')->where(['bclassid'=>$classid])->select();
            $this->assign('secondCate',$secondCate);
            $thirdCate = Db::table('e_downclass')->where(['bclassid'=>$sclassid])->select();
            $this->assign('thirdCate',$thirdCate);
        }
        if($tclassid){
            $where['a.classid'] = $tclassid;
            $thirdCate = Db::table('e_downclass')->where(['bclassid'=>$sclassid])->select();
            $this->assign('thirdCate',$thirdCate);
            $secondCate = Db::table('e_downclass')->where(['bclassid'=>$classid])->select();
            $this->assign('secondCate',$secondCate);
        }

        if(request()->isAjax()){
            $id = request()->post('id');
            $data = Db::table('e_downclass')->where(['bclassid'=>$id])->select();
            if($data){
                $msg = ['code'=>1,'info'=>'获取数据成功','data'=>$data];
            }else{
                $msg = ['code'=>0,'info'=>'获取数据失败'];
            }
            return json($msg);
        }
        //获取所有的一级分类
        $firstCate = Db::table('e_downclass')->where(['bclassid'=>0])->select();
        $down=db('down')->where($where)->field('a.*,b.classname')->alias('a')->join('e_downclass b','a.classid=b.classid')->order('a.softid desc')->paginate(10,false,['query'=>request()->param()]);
        $this->assign('downs',$down);
        $this->assign('firstCate',$firstCate);
        return view();
    }

    public function add(){
        //查询水晶作者
        $author = Db::table('e_downmember')->where(['userid'=>['in',[
            40434,40435,40436,40437,40438,40439,40440,40441,40442,40443,40444
        ]]])->select();
        //获取源码类型
        $softtype = Db::table('e_softtype')->select();
        //获取一级下载分类
        $downClass = SoftCate::NavCate();
        if(request()->isPost()){
            $data=input('post.');
            $data['softtime']=time();
            $data['checked'] = 1;
            $down=new DownModel;
//            $data['softsay'] = htmlspecialchars($data['softsay']);
            if($down->allowField(true)->save($data)){
                $this->success('添加文章成功',url('lst'));
            }else{
                $this->error('添加文章失败！');
            }
            return;
        }
        $downclass=new DownclassModel();
        $cateres=$downclass->catetree();
        $language=$downclass->language();
        $this->assign('author',$author);
        $this->assign('downClass',$downClass);
        $this->assign('language',$language);
        $this->assign('downclasss',$cateres);
        $this->assign('softtype',$softtype);
        return view();
    }

    //获取下载分类
    public function getDownClass()
    {
        if(request()->isAjax()){
            $classid = request()->post('classid');
            $data = SoftCate::getClassByPid($classid);
            if(!empty(ToArray($data))){
                $msg = ['code'=>1,'info'=>'获取分类成功','data'=>$data];
            }else{
                $msg = ['code'=>0,'info'=>'获取分类失败，请与管理人员联系'];
            }
            return json($msg);
        }
    }
    public function edit(){
        //查询水晶作者
        $author = Db::table('e_downmember')->where(['userid'=>['in',[
            40434,40435,40436,40437,40438,40439,40440,40441,40442,40443,40444
        ]]])->select();
        //获取源码类型
        $softtype = Db::table('e_softtype')->select();
        if(request()->isPost()){
            $data=input('post.');
            $down=new DownModel;
            $data = self::UallowField($data);
            $save=$down->update($data);
            if($save){
                $this->success('修改软件成功！',url('lst'));
            }else{
                $this->error('修改软件失败！');
            }
            return;
        }
        $downclass=new DownclassModel();
        $downclasss=$downclass->catetree();
        $downs=db('down')->where(array('softid'=>input('softid')))->find();
        $downs['softsay'] = htmlspecialchars_decode($downs['softsay']);
        $language=$downclass->language();

        //获取所有一级分类信息
        $cate = Db::table('e_downclass')->where(['bclassid'=>0])->select();
        $this->assign(array(
            'author'=>$author,
            'cate'=>$cate,
            'softtype'=>$softtype,
            'downclasss'=>$downclasss,
            'downs'=>$downs,
            'language'=>$language
            ));
        return view();
    }
    //获取父级分类
    private function getParentClass($id)
    {
        //获取父类信息
        $data = Db::table('e_downclass')->where(['classid'=>$id])->find();
        if($data['bclassid']){
            $this->getParentClass($data['bclassid']);
        }
        return $data['classid'];
    }
    public static function UallowField($data)
    {
        unset($data['waterword']);
        unset($data['thumbImg']);
        unset($data['updatePicTips']);
        unset($data['softSrc']);
        unset($data['updateSoftTips']);
        return $data;
    }
    public function del()
    {
        $data = db('down')->where(['softid'=>input('softid')])->find();
        Ftpupload::FtpDelete($data['downpath']);
        Ftpupload::FtpDelete($data['softpic']);
        if(DownModel::destroy(input('softid'))){

            $this->success('删除文章成功！',url('lst'));
        }else{
            $this->error('删除文章失败！');
        }
    }


 



   

	












}
