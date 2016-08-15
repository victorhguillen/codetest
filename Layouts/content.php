<body>
    <?php require('App/Controllers/GetFileController.php') ?>

    <?php

        $path = 'Storage/';

        $fileToLoad = 'posts.csv';

                  //CSV, SHORT, DETAILS, JSON
        $output = ['csv',null,'details',null];

        $getfile = new App\Controllers\GetFile($path, $fileToLoad, $output);

        $getfile->index();

    ?>
</body>
