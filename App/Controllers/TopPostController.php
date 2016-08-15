<?php

namespace App\Controllers;

include_once('OutputController.php');
include_once('DailyTopPostController.php');

/**
 * Create a new controller instance.
 * @return void
 */

Class TopPost {

    private $fp;
    private $top_posts;
    private $other_posts;
    private $top_posts_details;
    private $other_posts_details;
    public $first;
    public $first_o;

    /**
     * New controller instance.
     * @return void
     */
    Public function __construct(){

        $this->output = new Output;
        $this->toplikes = new TopLikes;
        $this->privacy = strtolower('public');

    }

    public function readFile($path, $file, $output){

        $top_posts = [];
        $other_posts = [];
        $top_posts_details = [];
        $other_posts_details = [];
        $first = true;
        $first_o = true;

        if (($read = fopen($file, "r")) !== FALSE) {

            while (($data = fgetcsv($read, 1000, ",")) !== FALSE) {
                /**
                 * TOP POST RULES
                 *
                 * @Privacy
                 * @Views
                 * @Comments
                 */
                if(($data[2] == $this->privacy) && ($data[5] > 10) && ($data[4] > 9000) && (strlen(utf8_decode($data[1])) < 40)){

                    if($first){
                        $top_posts[] = 'top_posts';
                        $top_posts_details[] = 'top_posts_details';
                    }

                    $top_posts[] = intval($data[0]);

                    $top_posts_details[] = $data;

                    $first=false;

                }else{
                    /**
                     * OTHER'S POST
                     *
                     */
                    if($first_o){
                        $other_posts[] = 'other_posts';
                        $other_posts_details[]= 'other_posts_details';
                    }

                    if($data[0] != 'id')
                    $other_posts[] = $data[0];

                    $other_posts_details[] = $data;

                    $first_o = false;

                }

            }

        }

        fclose($read);

        $data = [
            'top_post'           => $top_posts,
            'other_posts'        => $other_posts,
            'top_posts_details'  => $top_posts_details,
            'other_posts_details'=> $other_posts_details,
        ];

        $this->output->createFiles($path, $output, $data);

        $this->toplikes->dailyTopLikes($path, $file, $output);

        echo 'DONE';

    }

}
