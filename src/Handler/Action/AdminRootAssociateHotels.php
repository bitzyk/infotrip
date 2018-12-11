<?php

namespace Infotrip\Handler\Action;

use Infotrip\Domain\Repository\AuthUserRepository;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminRootAssociateHotels extends AbstractRootAdminPageAction
{
    /**
     * @var AuthUserRepository
     */
    private $authUserRepository;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->authUserRepository = $this->container->get(\Infotrip\Domain\Repository\AuthUserRepository::class);

    }


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

        $users = $this->authUserRepository
            ->getAll();

        $args['users'] = $users;

        return $this->renderer->render($response, 'hotelOwners/admin/root/associate-hotels.phtml', $args);
    }

}