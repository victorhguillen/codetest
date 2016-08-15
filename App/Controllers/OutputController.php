<?php

namespace App\Controllers;
/**
 * Create a new controller instance.
 * @return void
 */

Class Output {

    /**
     * New controller instance.
     * @return void
     */
    Public function __construct(){


    }

    public function createFiles($path, $output, $data) {

        foreach ($data as $posts) {

            // prettyArray($posts[0]);

            if($output[0]=='csv' && $output[1]=='short'){

                if($posts[0]=='top_posts')
                $this->createFile($path, $posts,isset($output[3]) ? 'json' : 'csv');

                if($posts[0]=='other_posts')
                $this->createFile($path, $posts,isset($output[3]) ? 'json' : 'csv');

            }

            if($output[0]=='csv' && $output[2] == 'details'){

                if($posts[0]=='top_posts_details')
                $this->createFileWithDetails($path, $posts,isset($output[3]) ? 'json' : 'csv');

                if($posts[0]=='other_posts_details')
                $this->createFileWithDetails($path, $posts,isset($output[3]) ? 'json' : 'csv');

            }

        }

    }

    public function createFile($path, $posts_id, $type){

        if($type=='json'){

            $fp = $path.$posts_id[0].".$type";

            $keys=[];
            for ($c=1; $c < count($posts_id); $c++){
                $keys[]=['post_id',$posts_id[$c]];
            }

            $json = array_values(array_map([$this, 'prettyJsonTop'], $keys));

            file_put_contents($fp, json_encode($json, JSON_PRETTY_PRINT));

        }else{

        $fp = fopen($path.$posts_id[0].".$type", 'w');

        foreach ($posts_id as $post) {
            $data[] = $post;
            fputcsv($fp, $data);
            unset($data);
        }

        fclose($fp);

        }

    }

    public function createFileWithDetails($path, $posts_details, $type){

        if($type=='json'){

            $fp = $path.$posts_details[0].".$type";

            $json = array_values(array_map([$this, 'prettyJsonTopDetails'], array_slice($posts_details,1)));

            file_put_contents($fp, json_encode($json, JSON_PRETTY_PRINT));

        }else{

            $fp = fopen($path.$posts_details[0].".$type", 'w');

            unset($posts_details[0]);

            foreach ($posts_details as $post) {
                $data[] = $post;
                fputcsv($fp, $post);
                unset($data);
            }

            fclose($fp);

        }

    }

    public function createTopLikesFile($path, $topLikes, $topLikesDetalis, $output){

        if($output[0]=='csv' && $output[1]=='short'){

            $type = ($output[3]) ? 'json' : 'csv';
            $details = '';

        }

        if($output[0]=='csv' && $output[2]=='details' && $output[1]==null){

            $type = ($output[3]) ? 'json' : 'csv';
            $details = 'details';
            $topLikes = $topLikesDetalis;

        }

        if($type=='json'){

            $fp = $path.'daily_top_posts'.$details.".$type";

            if($output[1]=='short'){

                $json = array_values(array_map([$this, 'prettyJsonTopLikes'], $topLikes));

            }else {

                $json = array_values($topLikes);

            }

            file_put_contents($fp, json_encode($json, JSON_PRETTY_PRINT));

        }else{

            $fp = fopen($path.'daily_top_posts'.$details.".$type", 'w');

            if($output[1]=='short'){

                fputcsv($fp,['id', 'likes', 'timestamp']);

            }else{

                fputcsv($fp,['id','title', 'privacy', 'likes', 'views', 'comments', 'timestamp']);

            }

            foreach ($topLikes as $post) {
                fputcsv($fp, $post);
            }

            fclose($fp);

        }

    }




    public function prettyJsonTop(array $data)
    {

        return [
            'id'    => intval($data[1]),
        ];

    }

    public function prettyJsonTopDetails(array $data)
    {

        return [
            'id'        => intval($data[0]),
            'title'     => $data[1],
            'privacy'   => $data[2],
            'likes'     => intval($data[3]),
            'views'     => intval($data[4]),
            'comments'  => $data[5],
            'timestamp' => $data[6],
        ];

    }

    public function prettyJsonTopLikes(array $data)
    {

        return [
            'id'        => intval($data['id']),
            'likes'      => intval($data['likes']),
            'timestamp' => $data['date'],
        ];

    }

    public function prettyJsonTopLikesDetails(array $data)
    {

        return [
            'id'        => intval($data['id']),
            'title'     => $data['tilte'],
            'privacy'   => $data['privacy'],
            'likes'     => intval($data['likes']),
            'views'     => intval($data['views']),
            'comments'  => $data['commnets'],
            'timestamp' => $data['date'],
        ];

    }

}
