<?php
/**
 * 萝卜音乐系统
 * @verson 1.0
 * @author: yanghuachu
 * @company: 萝卜科技
 * @license: yy.luobokeji.cn
 * @copyright: 2012 - 2019
 */

namespace app\index\controller;


use app\admin\model\Ftpupload;
use think\Db;

class Markdown extends Common
{
    //编写文章
    public function index()
    {
        if(!session('user_id')){
            echo "<script>alert('请先登录');history.go(-1);</script>";die;
        }
        //系统分类
        $systemCate = Db::table('e_cate')->where(['pid'=>0])->select();
        //用户自定义分类
        $userCate = Db::table('e_article_cate')->where(['uid'=>session('user_id')])->select();
        //用户自定义标签
        $userTag = Db::table('e_article_tag')->where(['uid'=>session('user_id')])->select();
        return view('',[
            'systemCate'=>$systemCate,
            'userCate'=>$userCate,
            'userTag'=>$userTag
        ]);
    }
    //文件上传
    public function upload()
    {
        if(isset($_FILES['editormd-image-file'])){
            $saveName = 'markdown/'.date('Ym',time()).'/'.$this->getRandomStr(16).'.'.substr(strrchr($_FILES['editormd-image-file']['name'], '.'), 1);
            $res = Ftpupload::upload($_FILES['editormd-image-file']['tmp_name'],'public_html/'.$saveName);
            if($res){
                $msg = ['success'=>1,'url'=>'http://file.662p.com/'.$saveName];
            }else{
                $msg = ['success'=>0,'message'=>'上传失败，请稍后再试'];
            }
            return json($msg);
        }
    }
    //文章发布
    public function publish()
    {
        if(!session('user_id')){
            return json(['code'=>0,'info'=>'请先登录']);
        }
        if($this->request->isAjax()){
            $data = $this->request->post();
            $tag = explode(',',$data['tag_ids']);
            //添加新的标签
            $tagIDs = [];
            if($data['tag_add']){
                $tagAdd = explode(',',$data['tag_add']);

                foreach ($tagAdd as $key=>$val){
                    $exist = Db::table('e_article_tag')->where(['name'=>$val,'uid'=>session('user_id')])->value('id');
                    if($exist){
                        $tagIDs[] = $exist;
                    }else{
                        $res = Db::table('e_article_tag')->insertGetId([
                            'name'=>$val,
                            'c_time'=>time(),
                            'uid'=>session('user_id')
                        ]);
                        if($res){
                            $tagIDs[] = $res;
                        }
                    }
                }


            }
            if($tagIDs&&$tag){
                $tag = array_merge($tagIDs,$tag);
            }else if($tagIDs&&(!$tag)){
                $tag = $tagIDs;
            }else if ((!$tagIDs)&&$tag){
                $tag = $tag;
            }else{
                $tag = $tag;
            }
            $cate = explode(',',$data['cate_ids']);
            //添加新的分类
            $cateIDs = [];
            if($data['cate_add']){
                $cateAdd = explode(',',$data['cate_add']);
                foreach ($cateAdd as $key=>$val){
                    $exist = Db::table('e_article_cate')->where(['name'=>$val,'uid'=>session('user_id')])->value('id');
                    if($exist){
                        $cateIDs[] = $exist;
                    }else{
                        $res = Db::table('e_article_cate')->insertGetId([
                            'name'=>$val,
                            'c_time'=>time(),
                            'uid'=>session('user_id')
                        ]);
                        if($res){
                            $cateIDs[] = $res;
                        }
                    }
                }
            }
            if($cateIDs&&$cate){
                $cate = array_merge($cateIDs,$cate);
            }else if($cateIDs&&(!$cate)){
                $cate = $cateIDs;
            }else if ((!$cateIDs)&&$cate){
                $cate = $cate;
            }else{
                $cate = $cate;
            }
            //上传图片
            if(isset($_FILES['theme'])){
                $saveName = 'user/article/'.date('Ym',time()).'/'.$this->getRandomStr(16).'.'.substr(strrchr($_FILES['theme']['name'], '.'), 1);
                $res = Ftpupload::upload($_FILES['theme']['tmp_name'],'public_html/'.$saveName);
                if($res){
                    $data['thumb'] = 'http://file.662p.com/'.$saveName;
                }
            }
            $user = Db::table('e_downmember')->where(['userid'=>session('user_id')])->find();
            //添加文章
            $data['userid'] = session('user_id');
            $data['author'] = $user['username'];
            $data['time'] = time();
            $data['index_rec'] = 0;
            unset($data['cate_ids']);
            unset($data['tag_ids']);
            unset($data['tag_add']);
            unset($data['cate_add']);
            //文章数据写入
            $result = Db::table('e_article')->insertGetId($data);
            if($result){
                //记录分类
                foreach ($cate as $key=>$val){
                    Db::table('e_article_cateids')->insertGetId([
                        'cate_id'=>$val,
                        'article_id'=>$result,
                        'c_time'=>time()
                    ]);
                }
                //记录标签
                foreach ($tag as $key=>$val){
                    Db::table('e_article_tagids')->insertGetId([
                        'tag_id'=>$val,
                        'article_id'=>$result,
                        'c_time'=>time()
                    ]);
                }
                $msg = ['code'=>1,'info'=>'发布成功'];
            }else{
                $msg = ['code'=>0,'info'=>'发布文章失败，请稍后再试'];
            }
            return json($msg);
        }
    }
    //获取随机字符串
    public function getRandomStr($len, $special=false){
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );

        if($special){
            $chars = array_merge($chars, array(
                "!", "@", "#", "$", "?", "|", "{", "/", ":", ";",
                "%", "^", "&", "*", "(", ")", "-", "_", "[", "]",
                "}", "<", ">", "~", "+", "=", ",", "."
            ));
        }

        $charsLen = count($chars) - 1;
        shuffle($chars);                            //打乱数组顺序
        $str = '';
        for($i=0; $i<$len; $i++){
            $str .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
        }
        return $str;
    }

}