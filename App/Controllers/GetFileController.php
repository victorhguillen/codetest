<?php

namespace App\Controllers;

include('App/Helpers/helpers.php');

include_once('CheckFileController.php');
include_once('TopPostController.php');

/**
 * Create a new controller for get the file.
 * @return void
 */

Class GetFile {

    protected $path;
    protected $file;
    public $output;

    Public function __construct($path, $file, $output){

        $this->path = $path;
        $this->file = $file;
        $this->output = $output;
        $this->checkFile = new CheckFile;
        $this->topPost = new TopPost;

    }

    public function index(){

        $path = $this->path;
        $file = $path.$this->file;

        /**
         * Check the file if is compatible
         * and Exist.
         */

        $check = $this->checkFile->fileType($file);

        if($check){

            $output = $this->output;

            /**
             * Read the file for generate the ouputs.
             *
             */
            $this->topPost->readFile($path, $file, $output);

        }else{

            $error_message = "The File no exist or is not compatible.";

            return $error_message;

        }

    }

}
