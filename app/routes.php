<?php

declare(strict_types = 1);

use App\Application\Actions\Breed\ListBreedAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->get('/breeds', function (Request $request, Response $response) {

        $db = new PDO('sqlite:./sqllite.db');
        //$table = $db->query('CREATE TABLE cache (cache_id INTEGER PRIMARY KEY,user_id INTEGER NOT NULL, response TEXT NOT NULL, busca TEXT NOT NULL);');
        $name = '';
        if (isset($_GET['name'])) {
            $name = $_GET['name'];
        }

        $data = $db->query("select * from cache where busca = '{$name}' and user_id = 1 order by cache_id limit 1");
        $data = $data->fetch();

        if (empty($data)) {

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.thecatapi.com/v1/breeds/search?q={$name}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 60,
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
                $db->query("INSERT INTO cache (user_id,response,busca) values(1,'$result','$name')");
                $response->getBody()->write($result);
                return $response;
            }
        } else {
            $response->getBody()->write($data['response']);
            return $response;
        }
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
