<?php
namespace app\admin\controller;
use app\admin\model\Downlink as DownlinkModel;
use app\admin\controller\Common;
class Downlink extends Common
{



    public function lst()
    {
        $link=new DownlinkModel();
        if(request()->isPost()){
            $sorts=input('post.');
            foreach ($sorts as $k => $v) {
                $link->update(['lid'=>$k,'myorder'=>$v]);
            }
            $this->success('更新排序成功！',url('lst'));
            return;
        }
        $linkres=$link->order('myorder asc')->paginate(10);
        $this->assign('linkres',$linkres);
        return view();
	}

    public function add(){
    if (!empty($_POST)){
      $post = input('post.');
      $data['ltime']=time();
      if (!empty($_FILES['lpic']['size'])) {
        if (isset($_FILES) && $_FILES['lpic']['error']==0 && !empty($_FILES)) {
          // 获取表单上传文件 例如上传了001.jpg
          $upload = request()->file('lpic');
          // 移动到框架应用根目录/uploads/ 目录下
          $info = $upload->move( './uploads/link/');
          //有图片上传 执行上传 判断是否上传成功 如果成功裁剪
              //执行信息修改    如果信息修改成功则删除旧图片 否则删除新上传的图片 
          if(!$info){
              return $this->error($upload->getError());
          }else{
              //上传图片成功
              $save['lpic'] = '/uploads/link/'.$info->getSaveName();   
          }
        }
        $post['lpic'] = $save['lpic'];
        $z = db('downlink')->insert($post);
        if (!empty($z)) {
            $this->success('添加成功！！！',Url('downlink/lst'));
        }else{
          if (!empty($_FILES['lpic']['size'])) {
            unlink('.'.$post['lpic']);
          }
          $this->error("添加失败！！！");
        }
      }else{
        $this->error('请上传图片！！！');
      }
    }
    return $this->fetch();
    }


    public function edit($lid){
    if(request()->isPost()){
            $data=input('post.');
            $validate = \think\Loader::validate('Link');
            if(!$validate->scene('edit')->check($data)){
                $this->error($validate->getError());
            }

       if (!empty($_FILES['lpic']['size']) && $_FILES['lpic']['error']==0 && !empty($_FILES)) {
        // 获取表单上传文件 例如上传了001.jpg
        $upload = request()->file('lpic');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $upload->move( './uploads/link/');
        //有图片上传 执行上传 判断是否上传成功 如果成功裁剪
            //执行信息修改    如果信息修改成功则删除旧图片 否则删除新上传的图片 
        if(!$info){
            return $this->error($upload->getError());
        }else{
            //上传图片成功
            $save['lpic'] = '/uploads/link/'.$info->getSaveName();   
            $data['lpic'] = $save['lpic'];
        }
      }

      $linkinfo = db('downlink')->field('lpic')->where('lid ='.$lid)->find();
      $z = db('downlink')->where('lid ='.$lid)->update($data);
      if (!empty($z)) {
        if (!empty($_FILES['lpic']['size'])&&!empty($linkinfo['lpic'])) {
          unlink('.'.$linkinfo['lpic']);
        }
        $this->success("修改成功",'downlink/lst');
      }else{
        if (!empty($_FILES['lpic']['size'])) {
          unlink('.'.$data['lpic']);
        }
        $this->error("修改失败！！！");
      }
      
      return;
    }
    $link = db('downlink')->where('lid ='.$lid)->find();
    $this->assign('links',$link);

        return $this->fetch();
    }
 
 //删除友链
    public function del(){
        $del=DownlinkModel::destroy(input('lid'));
        if($del){
           $this->success('删除链接成功！',url('lst')); 
        }else{
            $this->error('删除链接失败！');
        }
    }

    




   

	












}
