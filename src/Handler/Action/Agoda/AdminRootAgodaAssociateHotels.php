<?php

namespace Infotrip\Handler\Action\Agoda;

use Infotrip\Handler\Action\AbstractRootAdminPageAction;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminRootAgodaAssociateHotels extends AdminRootAbstractAgoda
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
            isset($args['assocLevel']) &&
            in_array((int) $args['assocLevel'], [1, 2, 3])
        ) {
            $agodaResponse = $this->agodaService->associateHotels((int) $args['assocLevel']);

            $successMessage = sprintf(
                'The execution of `%s` have been successfully. New association: `%s`',
                $agodaResponse->getNameAssociation(),
                $agodaResponse->getNewAssociations()

            );

            return $response
                ->withRedirect(
                    $this->routerHelper->getAdminRootAgodaAssociateHotelsUrl() . '?successMessage=' . $successMessage,
                    301
                );
        }

        return $this->renderer->render($response, 'hotelOwners/admin/root/agoda/agoda-association.phtml', $args);
    }
}