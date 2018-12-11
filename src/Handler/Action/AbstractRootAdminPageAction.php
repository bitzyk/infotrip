<?php

namespace Infotrip\Handler\Action;

use Slim\Http\Request;
use Slim\Http\Response;

abstract class AbstractRootAdminPageAction extends AbstractAdminPageAction {


    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return array|\Psr\Http\Message\ResponseInterface|Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, $args = [])
    {
        $parentResponse = parent::__invoke($request, $response, $args);

        if ($parentResponse instanceof \Slim\Http\Response) {
            return $parentResponse;
        } else if(is_array($parentResponse)) {
            $args = $parentResponse;
        }
        if (! $this->hotelOwnerUser->isRootUser()) {
            throw new \Exception('Invalid request');
        }

        return $args;
    }

}
