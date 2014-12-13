<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class FileUploader extends UploaderCore {

    private $_token;

    public function upload($file, $dest = null) {
        if ($this->validate($file)) {
            if (isset($dest) && is_dir($dest))
                $file_path = $dest;
            else
                $file_path = $this->getFilePath(isset($dest) ? $dest : $this->_token.'_'.time());

            if ($file['tmp_name'] && is_uploaded_file($file['tmp_name'] ))
                move_uploaded_file($file['tmp_name'] , $file_path);
            else
                // Non-multipart uploads (PUT method support)
                file_put_contents($file_path, fopen('php://input', 'r'));

            $file_size = $this->_getFileSize($file_path, true);
            $file['save_path'] = str_replace(_PS_UPLOAD_DIR_, '', $file_path);
        }

        return $file;
    }

    public function setToken($token) {
        $this->_token = $token;
    }

    public function remove($name) {
        $file_path = $this->getFilePath($this->_token.$name);
        unlink($file_path);
    }

    protected function validate(&$file) {
        //TODO
        return true;
    }
}
