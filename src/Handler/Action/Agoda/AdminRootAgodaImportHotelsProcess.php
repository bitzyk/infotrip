<?php

namespace Infotrip\Handler\Action\Agoda;

use Infotrip\Handler\Action\AbstractRootAdminPageAction;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminRootAgodaImportHotelsProcess extends AdminRootAbstractAgoda
{

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

        if (
            isset($_FILES['importFile']['tmp_name']) &&
            $_FILES['importFile']['tmp_name']
        ) {
            $importResponse = $this->agodaService
                ->importHotels($_FILES['importFile']['tmp_name']);

            print_r($importResponse);
            exit;
        }

        echo 'aici'; exit;

    }
}