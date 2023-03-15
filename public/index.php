<?php

// Подключение автозагрузки через composer
require __DIR__ . "/../vendor/autoload.php";

use Slim\Factory\AppFactory;
use Slim\Routing\RouteContext;
use Slim\Views\PhpRenderer;
use DI\Container;
use Valitron\Validator;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use DiDom\Document;
use DiDom\Query;
use Hexlet\Code\Misc;

use PostgreSQLTutorial\Connection;
use PostgreSQLTutorial\PostgreSQLCreateTable;

session_start();

if (!isset($_SESSION["start"])) {
    $pdo = Connection::get()->connect();

    if (Misc\tableExists($pdo, "urls")) {
        $tableCreator = new PostgreSQLCreateTable($pdo);
        $tableCreator->createTables();
    }
    $_SESSION["start"] = true;
}

try {
    $pdo = Connection::get()->connect();
    if (!Misc\tableExists($pdo, "urls")) {
        $tableCreator = new PostgreSQLCreateTable($pdo);
        $tableCreator->deleteAllTable();
    }
} catch (\PDOException $e) {
    echo $e->getMessage();
}

$container = new Container();
$container->set("renderer", function () {
    return new PhpRenderer(__DIR__ . "/templates");
});

$container->set("flash", function () {
    return new \Slim\Flash\Messages();
});

$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

// INDEX
$app->get("/urls", function ($request, $response) {
    $pdo = Connection::get()->connect();
    $tableadd = new PostgreSQLCreateTable($pdo);
    $usrls = $tableadd->getUrls();

    $urlsCheck = $tableadd->getLastChekTime();
    $res = array_reverse(
        array_map(function ($item) use ($urlsCheck) {
            foreach ($urlsCheck as $chek) {
                if ($item["id"] === $chek["url_id"]) {
                    $item["last_mod"] = $chek["created_at"];
                    $item["status_code"] = $chek["status_code"];
                }
            }
            return $item;
        }, $usrls)
    );
    //arsort($usrls);
    $params = [
        "usrls" => $res,
    ];

    return $this->get("renderer")->render($response, "index.phtml", $params);
})->setName("urls.index");
//NAV
$app->get("/", function ($request, $response) {
    $params = [
        "url" => [],
    ];
    return $this->get("renderer")->render($response, "nav.phtml", $params);
})->setName("urls.nav");

// SHOW
$app->get("/urls/{id}", function ($request, $response, $args) {
    $pdo = Connection::get()->connect();
    $id = $args["id"];
    $tableadd = new PostgreSQLCreateTable($pdo);

    $sql = "SELECT * FROM urls WHERE id = {$id};";
    $stmt = $pdo->query($sql)->fetch(\PDO::FETCH_ASSOC);
    $url = $stmt;

    $messages = $this->get("flash")->getMessages();

    $chekingsUrl = $tableadd->getCheckForCurrentUrl($id);

    $count = count($chekingsUrl);

    $params = [
        "url" => $url,
        "flash" => $messages,
        "chekingsUrl" => $chekingsUrl,
        "count" => $count,
    ];

    return $this->get("renderer")->render($response, "show.phtml", $params);
})->setName("urls.show");

// POST
$app->post("/urls", function ($request, $response) {
    $pdo = Connection::get()->connect();
    $tableUrls = new PostgreSQLCreateTable($pdo);
    $routeParser = RouteContext::fromRequest($request)->getRouteParser();

    $urlData = $request->getParsedBody();

    $validator = new Validator($urlData["url"]);
    $validator
        ->rule("required", "name")
        ->message("URL не должен быть пустым")
        ->label("name");
    $validator
        ->rule("url", "name")
        ->message("Некорректный URL")
        ->label("name");

    if ($validator->validate()) {
        $domen = parse_url($urlData["url"]["name"]); //нормализация url
        $urlData["url"]["name"] = $domen["scheme"] . "://" . $domen["host"];
        $dataNow = Carbon::now();
        $urlData["url"]["created_at"] = $dataNow->toDateTimeString();

        $sql = "SELECT * FROM urls WHERE name = '{$urlData["url"]["name"]}';";
        $stmt = $pdo->query($sql)->fetch(\PDO::FETCH_ASSOC);

        $findUrl = $stmt; // поиск в базе url
        if (!is_array($findUrl)) {
            $tables = $tableUrls->addUrl($urlData["url"]); // добавляем в базу

            $sql = "SELECT * FROM urls ORDER BY id DESC, id DESC LIMIT 1;";
            $stmt = $pdo->query($sql)->fetch(\PDO::FETCH_ASSOC);
            $lastUrl = $stmt; // ищем последнюю добавленную запись

            $url = $routeParser->urlFor("urls.show", ["id" => $lastUrl["id"]]);
            $this->get("flash")->addMessage(
                "success",
                "Страница успешно добавлена"
            );
            return $response->withHeader("Location", $url)->withStatus(302);
        } else {
            $url = $routeParser->urlFor("urls.show", ["id" => $findUrl["id"]]);
            $this->get("flash")->addMessage(
                "noishave",
                "Страница уже существует"
            );
            return $response->withHeader("Location", $url)->withStatus(302);
        }
    } else {
        $errors = $validator->errors();
        $params = [
            "errors" => $errors,
        ];
        $response = $response->withStatus(422);
        return $this->get("renderer")->render($response, "nav.phtml", $params);
    }
})->setName("urls.post");

// POST Проверки
$app->post("/urls/{url_id}/checks", function ($request, $response, $args) {
    $pdo = Connection::get()->connect();
    $tableUrls = new PostgreSQLCreateTable($pdo);

    $sql = "SELECT * FROM urls WHERE id = {$args["url_id"]};";
    $stmt = $pdo->query($sql)->fetch(\PDO::FETCH_ASSOC);
    $url = $stmt["name"];

    try {
        $client = new Client(["base_uri" => $url, "verify" => false]);
        $response = $client->get($url);

        $document = new Document($url, true);
        $h1 = optional($document->first("h1"));
        $title = optional($document->first("title"));
        $desc = optional($document->first("meta[name=description]"));

        $dataNow = Carbon::now();
        $data = [
            "url_id" => $args["url_id"],
            "created_at" => $dataNow->toDateTimeString(),
            "h1" => $h1->text(),
            "title" => $title->text(),
            "description" => $desc->getAttribute("content") ?? "",
            "status_code" => $response->getStatusCode(),
        ];
        $tableUrls->addChek($data);
        $this->get("flash")->addMessage(
            "successchek",
            "Страница успешно проверена"
        );
    } catch (TransferException $e) {
        $this->get("flash")->addMessage(
            "failure",
            "Произошла ошибка при проверке, не удалось подключиться"
        );
    }

    $routeParser = RouteContext::fromRequest($request)->getRouteParser();
    $url = $routeParser->urlFor("urls.show", ["id" => $args["url_id"]]);

    return $response->withHeader("Location", $url)->withStatus(302);
})->setName("url_checks.post");

$app->run();
