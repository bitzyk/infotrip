<?php

namespace Infotrip\Handler\Action;

use Slim\Http\Request;
use Slim\Http\Response;

class AdminAddNewHotel extends AbstractAdminPageAction
{

    public function __invoke(Request $request, Response $response, $args = [])
    {
        $parentResponse = parent::__invoke($request, $response, $args);

        if ($parentResponse instanceof \Slim\Http\Response) {
            return $parentResponse;
        } else if(is_array($parentResponse)) {
            $args = $parentResponse;
        }

        return $this->renderer->render($response, 'hotelOwners/admin/social-media.phtml', $args);
    }

}