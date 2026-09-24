<!DOCTYPE html>
<html>

<head>
    <title>Cover Page</title>
    <style>
        @page :first {
            header {
                display: none;
            }

            footer {
                display: none;
            }

        }

        body{
            padding: 0px !important;
            margin: 0px !important;
        }

        .cover-img{
            background-image: url("{{ public_path('theme/dist/img/cover-img.png') }}");
            background-repeat: no-repeat;
            background-position: 100% 100vh;
            left: 0px;
        }

    </style>
</head>

<body>

    <div class="container-fluid cover-img">

        <h3>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Suscipit aspernatur aliquam officia, voluptatum sunt atque repudiandae magnam magni minus corporis at qui excepturi in necessitatibus quibusdam tempore dignissimos fugit molestias repellat quam cupiditate consequuntur ea totam! Quisquam quasi, repellat officia quas enim explicabo. Consequatur laboriosam eveniet dolorem, fugit deserunt necessitatibus?Lorem ipsum,i!</h3>

        <h3>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Suscipit aspernatur aliquam officia, voluptatum sunt atque repudiandae magnam magni minus corporis at qui excepturi in necessitatibus quibusdam tempore dignissimos fugit molestias repellat quam cupiditate consequuntur ea totam! Quisquam quasi, repellat officia quas enim explicabo. Consequatur laboriosam eveniet dolorem, fugit deserunt necessitatibus?Lorem ipsum,i!</h3>

        <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Necessitatibus vitae aspernatur atque eligendi architecto itaque, quae ratione, perferendis consequuntur repellendus molestias deleniti porro, ex odit. Perferendis, facere laudantium. Doloribus, illum!</p>

    </div>

</body>

</html>
