<?php

declare(strict_types = 1);

use App\Application\Actions\Breed\ListBreedAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->get('/breeds', function (Request $request, Response $response) {

        $name = $_GET['name'];


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.thecatapi.com/v1/breeds/search?q={$name}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "x-api-key: 10b9acd1-1eaf-43a3-bb83-c3015ec3840f"
            ),
        ));

        $result = curl_exec($curl);
        $err = curl_error($curl);


        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response->getBody()->write($result);
            return $response;
        }
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
