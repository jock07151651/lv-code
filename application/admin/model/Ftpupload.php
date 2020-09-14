<?php

/**

 * Created by PhpStorm.

 * User: 华初

 * Date: 2019/2/27

 * Time: 9:43

 */



namespace app\admin\model;





use think\Model;



class Ftpupload extends Model

{

    /**

     * @param string $fileName      上传文件的tmp_name

     * @param string $savePath      文件的保存路径

     * @return \think\response\Json

     */

    public static function upload($fileName='',$savePath='')

    {

        import('ftpUpload.Ftp', VENDOR_PATH, '.php');

        $config = [

            'host' => '121.201.23.179',

            'user' => 'filecode662p',

            'pass' => 'code662pftp619'

        ];

        $ftp = new \Ftp($config);

        $result = $ftp->connect();

        if (!$result) {

            return json(['code' => '201', '连接失败']);

        }

        //本地文件路径

        $local_file = $fileName;

        //线上文件保存路径

        $remote_file = $savePath;

        //开始上传

        $res = $ftp->upload($local_file, $remote_file);

        return $res;

    }

    //上传处理



    /**

     * @param string $fileName 上传文件的tmpName名

     * @param string $saveType 保存的一级目录：例如img、video、soft等等

     * @param string $ext  上传文件的name

     * @return bool|string

     */

    public static function uploadFile($fileSrc='',$saveType='',$ext='')

    {

        //获取上传文件后缀

        $file_ext = self::getImageExt($fileSrc);

        $type = strtolower($file_ext);

        if(!$type){

            $file_ext = self::getImageExt($ext);

            $type = strtolower($file_ext);

        }

        //文件保存路径

        $saveName = $saveType.'/'.date('Y',time()).date('m',time()).'/'.date('YmdHis',time()).mt_rand(10000,99999).'.'.$type;

        $savePath = 'public_html/'.$saveName;

        //ftp上传

        $res = self::upload($fileSrc,$savePath);

        if($res){

            $filePath='http://file.662p.com/'.$saveName;

            return $filePath;

        }

        return false;

    }

    //ftp删除文件

    public static function FtpDelete($src)

    {

        import('ftpUpload.Ftp', VENDOR_PATH, '.php');

        $config = [

            'host' => '121.201.23.179',

            'user' => 'filecode662p',

            'pass' => 'code662pftp619'

        ];

        $ftp = new \Ftp($config);

        $result = $ftp->connect();

        if(!$result){

            return false;

        }

        $src = self::getRelativeUrl($src);

        return $ftp->delete_file($src);

    }

    //获取文件后缀

    private static function getImageExt($name)

    {

        return substr(strrchr($name, '.'), 1);

    }

    //获取根据域名后缀获取文件相对路径

    private static function getRelativeUrl($src,$ext='com',$Catalog='public_html')

    {

        return '.'.DS.$Catalog.substr(substr($src,strpos($src,$ext)),strlen($ext));

    }

}