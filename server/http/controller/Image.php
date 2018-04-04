<?php
namespace server\http\controller;
class Image extends Auth {

    public function create()
    {
        $file = $this->request->files['file'];
        if (substr($file["type"],0,5) != "image") {
            return ['status' => 0];
        }
        $ext = pathinfo($file['name'])['extension'];
        $imagePath = BASE_ROOT."/".STATIC_DIR."/chat/";
        if(!is_dir($imagePath)){
            mkdir($imagePath,0777,true);
        }
        $fileName = md5($file['name'].time()).".$ext";
        $newFile = $imagePath.$fileName;
        if (copy($file['tmp_name'],$newFile)){
            return [
                'status'=>1,
                'url'=>BASE_URL."/".STATIC_DIR."/chat/".$fileName
            ];
        } else{
            return ['status' => 0];
        }

    }
}