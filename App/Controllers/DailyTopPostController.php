<?php

namespace App\Controllers;

include_once('OutputController.php');

/**
 * Create a new controller instance.
 * @return void
 */

Class TopLikes {

    /**
     * New controller instance.
     * @return void
     */
    Public function __construct(){

        $this->output = new Output;

    }

    public function dailyTopLikes($path, $file, $output){

    $daily_top_posts_liked=[];
    $post_id=[];
    $likes=[];
    $likesDetails=[];
    $topLikes=[];
    $topLikesDetails=[];
    $l=0;
    $id=0;
    $first=true;
    $sort = [];

    if (($read = fopen($file, "r")) !== FALSE) {

        while (($data = fgetcsv($read, 1000, ",")) !== FALSE) {

             $likes[] = [
                            'id'    =>  intval($data[0]),
                            'title' =>  $data[1],
                            'privacy' =>  $data[2],
                            'likes' =>  intval($data[3]),
                            'views' =>  intval($data[4]),
                            'comments' =>  intval($data[5]),
                            'timestamp' =>  substr_replace($data[6], '', 11, 9)
                        ];

        }

        unset($likes[0]);

        /**
         * Get the unique dates for search the likes
         *
         */
        $unique_dates = array_unique(array_column($likes, 'timestamp'));

        /**
         * Sorting the dates
         *
         */
        arsort($unique_dates);

        foreach ($unique_dates as $date){

            foreach($likes as $like){

                if($date == $like['timestamp']){

                    if($l < $like['likes']){

                        $id = $like['id'];
                        $title = $like['title'];
                        $privacy = $like['privacy'];
                        $l = $like['likes'];
                        $views = $like['views'];
                        $comments = $like['comments'];
                        $topDate = $like['timestamp'];

                    }

                }

            }

            $topLikes[] = [
                            'id'    =>  $id,
                            'likes'  =>  $l,
                            'date'  =>  $topDate
                        ];

            $topLikesDetails[] = [
                                    'id'    =>  $id,
                                    'title' =>  $title,
                                    'privacy'   =>  $privacy,
                                    'likes'  =>  $l,
                                    'views' =>  $views,
                                    'comments' =>  $comments,
                                    'date'  =>  $topDate
                                ];

            $l = 0;
            $id = 0;
            $topDate = '';

        }

        foreach ($topLikes as $key => $row) {

            $sort['id'][$key]   =   $row['id'];
            $sort['likes'][$key] =   $row['likes'];
            $sort['date'][$key] =   $row['date'];

        }

        array_multisort($sort['likes'], SORT_DESC, SORT_NUMERIC, $topLikes);

        foreach ($topLikesDetails as $key => $row) {

            $sort['id'][$key]   =   $row['id'];
            $sort['title'][$key]   =   $row['title'];
            $sort['privacy'][$key] =   $row['privacy'];
            $sort['likes'][$key] =   $row['likes'];
            $sort['views'][$key] =   $row['views'];
            $sort['comments'][$key] =   $row['comments'];
            $sort['date'][$key] =   $row['date'];

        }

        array_multisort($sort['likes'], SORT_DESC, SORT_NUMERIC, $topLikesDetails);

        $this->output->createTopLikesFile($path, $topLikes, $topLikesDetails, $output);

        }

    }

}
