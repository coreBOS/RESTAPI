<?php
/**
 * This file is part of the Evolutivo Framework.
 *
 * For the full license information, view the LICENSE file that was distributed with this source code.
 *************************************************************************************************/
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Middleware\AddJsonResponseHeader;
use App\Middleware\RequireAPIKey;
use App\Controllers\Module;
use App\Controllers\MassOps;
use App\Controllers\WSAPI;

$app->get(
	'/',
	function (Request $request, Response $response, $args) {
		$response->getBody()->write('EvolutivoFW/coreBOS RESTful API access!');
		return $response->withStatus(418);
	}
);

$app->get(
	'/auth/{username}/{password}',
	[WSAPI::class, 'login']
)->add(AddJsonResponseHeader::class);

$app->get(
	'/query/{query:.*}',
	[WSAPI::class, 'query']
)->add(RequireAPIKey::class)->add(AddJsonResponseHeader::class);

$app->get(
	'/describe/'.SPEC_MODNAMES,
	[WSAPI::class, 'describe']
)->add(RequireAPIKey::class)->add(AddJsonResponseHeader::class);

$app->get(
	'/listtypes',
	[WSAPI::class, 'listtypes']
)->add(RequireAPIKey::class)->add(AddJsonResponseHeader::class);

$app->group(
	'/mass/',
	function (RouteCollectorProxy $group) {
		// $group->post('create', [MassOps::class, 'create']);
		$group->get('retrieve/'.SPEC_IDS, [MassOps::class, 'list']);
		// $group->patch('update', [MassOps::class, 'update']);
		$group->delete('delete/'.SPEC_IDS, [MassOps::class, 'delete']);
	}
)->add(RequireAPIKey::class)->add(AddJsonResponseHeader::class);

$app->group(
	'/'.SPEC_MODNAME,
	function (RouteCollectorProxy $group) {
		$group->get('', [Module::class, 'list']);
		$group->post('', [Module::class, 'create']);
		$group->patch('', Module::class . ':update');
		$group->group(
			'/' . SPEC_ID,
			function (RouteCollectorProxy $group) {
				$group->get('', Module::class . ':retrieve');
				$group->delete('', Module::class . ':delete');
			}
		);
	}
)->add(RequireAPIKey::class)->add(AddJsonResponseHeader::class);
