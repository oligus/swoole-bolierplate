<?php declare(strict_types=1);

require_once realpath(__DIR__ . '/..') . '/src/bootstrap.php';

use Swoole\Http\Server;
use SwooleTest\Response as GQLResponse;

$http = new \swoole_http_server("localhost", 8089);

$http->on('request', function (\Swoole\Http\Request $request, $response) {

    if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
        $rawBody     = $request->rawContent();
        $requestData = json_decode($rawBody ?: '', true);
    } else {
        $requestData = $request->post;
    }

    $query = isset($requestData['query']) ? $requestData['query'] : null;
    $variables = isset($requestData['variables']) ? $requestData['variables'] : null;

    $gqlResponse = new GQLResponse([
        'query' => $query,
        'variables' => $variables
    ]);

    $result = json_encode($gqlResponse->get());

    $response->end($result);
});

$http->start();




