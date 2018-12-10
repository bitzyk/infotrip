<?php

namespace Infotrip\Handler\Action;

use Infotrip\Utils\UI\Admin\Admin;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class AbstractAdminPageAction extends Action
{
    /**
     * @var \PHPAuth\Auth
     */
    protected $authService;

    /**
     * @var Admin
     */
    protected $adminUiService;

    /**
     * @var \Infotrip\ViewHelpers\RouteHelper
     */
    protected $routerHelper;

    /**
     * @var \Infotrip\Domain\Entity\HotelOwnerUser
     */
    protected $hotelOwnerUser;


    public function __construct(
        ContainerInterface $container
    )
    {
        parent::__construct($container);

        /** @var \PHPAuth\Auth $authService */
        $this->authService = $this->container->get(\PHPAuth\Auth::class);

    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Slim\Http\Response|array
     */
    public function __invoke(Request $request, Response $response, $args = [])
    {
        $routeHelperNamespace = $this->container->get(\Infotrip\ViewHelpers\RouteHelper::class);
        $this->routerHelper = $routeHelperNamespace($request);

        // do not allow access if is not logged
        if (!$this->authService->isLogged()) {
            return $response
                ->withRedirect(
                    $this->routerHelper->getHotelOwnerLoginRegisterUrl() . '?loginError=Login session has expired.',
                    301
                );
        }

        // set dependency
        $adminUiClosure = $this->container->get(\Infotrip\Utils\UI\Admin\Admin::class);
        /** @var Admin $adminUiService */
        $adminUiService = $adminUiClosure($request);
        $this->adminUiService = $adminUiService;

        $viewHelpers = $this->container->get('viewHelpers');
        $args['viewHelpers'] = $viewHelpers($request);
        $args['adminUiService'] = $this->adminUiService;

        /** @var \Infotrip\Domain\Repository\UserHotelRepository $userHotelRepository */
        $userHotelRepository = $this->container->get(\Infotrip\Domain\Repository\UserHotelRepository::class);

        $currentUser = $this->authService->getCurrentUser();

        $this->hotelOwnerUser = new \Infotrip\Domain\Entity\HotelOwnerUser();

        $this->hotelOwnerUser
            ->setUserId($currentUser['uid'])
            ->setEmail($currentUser['email']);

        $userHotelRepository
            ->setUserAssociatedHotels($this->hotelOwnerUser);

        $args['hotelOwnerUser'] = $this->hotelOwnerUser;
        $args['breadcrumb'] = $this->adminUiService->getAdminBreadcrumb()->buildBreadcrumb();
        $args['successMessage'] = $request->getParam('successMessage');

        return $args;
    }
}