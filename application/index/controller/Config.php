<?php
namespace app\Controller;
use think\Controller;
class Config extends Controller {

    public function upload(){
        if (IS_POST){
            foreach ($_FILES as $key => $value){
                $img = handleImg($key);
                $furl = C('REMOTE_ROOT').$img;
                if ($img){
                    ftp_upload($furl,$img);
                    $saveData['value'] = $img;
                    M('conf')
                        ->where("tag = '".$key."'")
                        ->save($saveData);
                }
            }
            $this->success('FTP 测试完成',U('Config/upload'),2);
        }else{
            $imgUrl = M('conf')
                ->where("tag = 'upImg'")
                ->getField('value');
            $this->assign('imgUrl',$imgUrl);
            $this->display();
        }

         return view();
    }


}