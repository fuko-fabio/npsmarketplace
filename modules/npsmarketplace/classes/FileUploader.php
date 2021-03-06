<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class FileUploader extends UploaderCore {

    public function upload($file, $dest = null) {
        syslog(LOG_DEBUG, 'Uploading file...');
        if ($this->validate($file)) {
            if (isset($dest) && is_dir($dest))
                $file_path = $dest;
            else
                $file_path = $this->getFilePath(isset($dest) ? $dest : $this->getUniqueFileName(''));

            if ($file['tmp_name'] && is_uploaded_file($file['tmp_name'] ))
                move_uploaded_file($file['tmp_name'] , $file_path);
            else
                // Non-multipart uploads (PUT method support)
                file_put_contents($file_path, fopen('php://input', 'r'));

            $file_size = $this->_getFileSize($file_path, true);
            $file['save_path'] = str_replace(_PS_UPLOAD_DIR_, '', $file_path);
            syslog(LOG_DEBUG, 'New file uploaded: '.$file_path.' file info: '.implode(' | ', $file));
        } else {
            syslog(LOG_WARNING, 'Invalid file: '.implode(' | ', $file));
        }
        return $file;
    }

    public function remove($name) {
        $file_path = $this->getFilePath($name);
        syslog(LOG_DEBUG, 'Removing uploaded file: '.$file_path);
        unlink($file_path);
    }
}
