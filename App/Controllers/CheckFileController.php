<?php

namespace App\Controllers;


/**
* Create a new controller for Check the file.
*
* @return void
*/

Class CheckFile {

    protected $file;
    protected $pathInfo;

    /**
    * New controller instance.
    * @return void
    */
    Public function __construct(){


    }

    /**
    * Determinate the File Type fist.
    * @return void
    */
    public function fileType($file){

        if(file_exists($file)){

            $this->pathInfo = pathinfo($file);

            $filesize = (filesize($file) <= 2097152) ? true : false;

            if($filesize){

                $extension = strtolower($this->pathInfo['extension']);

                $ext = ($extension == 'csv') ? true : false;

                return $ext;

            }

            return false;

        }else{

            return false;

        }

    }


}
