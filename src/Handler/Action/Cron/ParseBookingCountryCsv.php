<?php

namespace Infotrip\Handler\Action\Cron;

use Infotrip\Handler\Action\Action;
use Slim\Http\Request;
use Slim\Http\Response;

class ParseBookingCountryCsv extends Action
{

    public function __invoke(Request $request, Response $response, $args = [])
    {

        echo __METHOD__; exit;

        return $this->renderer->render($response, 'hotelOwners/admin/social-media.phtml', $args);
    }

}