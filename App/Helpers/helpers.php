<?php

    function dd($data){

        echo '<pre>';

            die(var_dump($data));

        echo '</pre>';

    }

    function prettyArray($data){

        echo '<pre>';

            print_r($data);

        echo '</pre>';

    }
