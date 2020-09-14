<?php
/**
 * 用户管理
 */
namespace app\admin\controller;
use app\admin\model\Cat_tree;
class Member extends Common
{


    /**
	 * 用户列表
	 * @return [type] [description]
	 */
    public function showmember()
    {
    	/*创建查询数据*/
    	$member = db('downmember')->field('userid,userpic,username,email,registertime')->order('registertime','desc' )->paginate(8);
    	$this->assign('showmember',$member);
    	$page = $member->render();
    	$this->assign('page',$page);
        return $this->fetch();
    }
     /**
     * 添加产品
     * @return [type] [description]
     */
    public function addmember()
    {
        if (!empty($_POST)) {
            $member = db('downmember');
            $post = input('post.');
            $post['registertime'] = time();
            $post['registerip'] = $_SERVER['REMOTE_ADDR'];
            $post['password']=md5($_POST['password']);
            $z=$member->insert($post);
            if(isset($z)){
                $this->success("添加成功",Url("Member/showmember"));

            }else{

                $this->error("添加失败");
            }

        } 
        return $this->fetch();
    } 


    //删除用户
        public function delmember($userid)
    {
        $result = db('downmember')->delete($userid);
        if ($result) {
            $this->success('删除成功！','Member/showmember');
        }else{
            $this->success('删除失败！');
        }
        return $this->fetch();
    }

    //修改会员
    public function updMember($userid) {
      if (!empty($_POST)){
      $_post = input("post.");

      if (!empty($_FILES['userpic']['size']) && $_FILES['userpic']['error']==0 && !empty($_FILES)) {
        // 获取表单上传文件 例如上传了001.jpg
        $upload = request()->file('userpic');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $upload->move( './uploads/link/');
        //有图片上传 执行上传 判断是否上传成功 如果成功裁剪
            //执行信息修改    如果信息修改成功则删除旧图片 否则删除新上传的图片 
        if(!$info){
            return $this->error($upload->getError());
        }else{
            //上传图片成功
            $save['userpic'] = '/uploads/link/'.$info->getSaveName();   
            $data['userpic'] = $save['userpic'];
        }
      }
      $data['username'] = $_post['username'];
      $data['password'] =md5($_post['password']);
      $data['email'] = $_post['email'];
      $data['sex'] = $_post['sex'];
      $data['mobile'] = $_post['mobile'];
      $data['checked'] = $_post['checked'];
      $linkinfo = db('downmember')->field('userpic')->where('userid ='.$userid)->find();
      $z = db('downmember')->where('userid ='.$userid)->update($data);
      if (!empty($z)) {
        if (!empty($_FILES['userpic']['size'])&&!empty($linkinfo['userpic'])) {
          unlink('.'.$linkinfo['userpic']);
        }
        $this->success("修改成功！",'Member/showmember');
      }else{
        if (!empty($_FILES['userpic']['size'])) {
          unlink('.'.$data['userpic']);
        }
        $this->error("修改失败！！！");
      }
      return;
    }
    $downmember = db('downmember')->where('userid ='.$userid)->find();
    $this->assign('downmember',$downmember);

    return $this->fetch();
    }

   

}


