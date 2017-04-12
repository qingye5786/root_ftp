<?php
/**
 * 微站页面
 * @author chenyanphp@qq.com
 */
defined('IN_IA') or exit('Access Denied');

class Root_FtpModuleSite extends WeModuleSite
{
    private $_max_size = 20; // 最大20字节，实际微信MP_*.txt 只有16字节
    private $_file_type = 'txt'; // 只允许上传txt文件
    private $_error_code = [ // 错误码
        0 => '没有错误发生，文件上传成功',
        1 => '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值',
        2 => '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
        3 => '文件只有部分被上传',
        4 => '没有文件被上传'
    ];

    /**
     * Ftp具体上传操作
     */
    public function doWebDo()
    {
        if ($_POST['sub']) {
            $file = $_FILES['thefile'];
            if ($file) {
                // 若所有检测都通过
                if ($this->_checkError($file['error']) &&
                    $this->_checkMaxSize($file['size']) &&
                    $this->_checkFileType(pathinfo($file['name'])) &&
                    $this->_chekFileIsExists($file['name']))
                {
                    if (move_uploaded_file($file['tmp_name'], IA_ROOT.'/'.$file['name'])) {
                        echo '<script>alert("上传成功!")</script>';
                    } else {
                        echo '<script>alert("上传失败!")</script>';
                    }
                }
            }
        }
        include $this->template("ftp_do");
    }

    /**
     * 检测文件上传是否出错
     *
     * @param $error_code
     * @return bool
     */
    private function _checkError($error_code)
    {
        if ($error_code > 0) {
            echo '<script>alert("'.$this->_error_code[$error_code].'");</script>';
            return false;
        }
        return true;
    }

    /**
     * 检测文件大小是否符合要求
     *
     * @param $size
     * @return bool
     */
    private function _checkMaxSize($size)
    {
        if ($size > $this->_max_size) {
            echo '<script>alert("上传文件大小必须在20字节以内！");</script>';
            return false;
        }
        return true;
    }

    /**
     * 检测文件格式是否符合要求
     *
     * @param $type
     * @return bool
     */
    private function _checkFileType($type)
    {
        if ($type['extension'] != $this->_file_type) {
            echo '<script>alert("上传文件格式必须为txt！");</script>';
            return false;
        }
        return true;
    }

    /**
     * 检测文件是否已存在
     *
     * @param $filename
     * @return bool
     */
    private function _chekFileIsExists($filename)
    {
        if (file_exists('/'.$filename)) {
            echo '<script>alert("文件已存在，请勿再次上传！");</script>';
            return false;
        }
        return true;
    }
}