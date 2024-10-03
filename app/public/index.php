<?php
/**
 * This file is part of the Evolutivo Framework.
 *
 * For the full license information, view the LICENSE file that was distributed with this source code.
 *************************************************************************************************/
declare(strict_types=1);

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
require_once '../config/globals.php';

use App\Middleware\Error404NotFoundHandler;
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;

require APP_ROOT . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));

$dotenv->load();

$builder = new ContainerBuilder();

$container = $builder->addDefinitions(APP_ROOT . '/config/definitions.php')->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$error_middleware = $app->addErrorMiddleware(true, true, true);
$error_middleware->setErrorHandler(\Slim\Exception\HttpNotFoundException::class, Error404NotFoundHandler::class);
$error_handler = $error_middleware->getDefaultErrorHandler();
$error_handler->forceContentType('application/json');

require APP_ROOT . '/config/routes.php';

$app->run();
