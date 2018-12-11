<?php

namespace Infotrip\Handler\Action;

use Infotrip\Domain\Entity\Hotel;
use Infotrip\Domain\Repository\HotelRepository;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminAcountSettingsProcess extends AbstractAdminPageAction
{

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
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


        $data = $request->getParsedBody();

        // change password
        if (
            isset($data['password']) && isset($data['password1'])  && isset($data['currPassword']) &&
            (strlen($data['password']) > 0) &&
            ($data['password'] === $data['password1'])
        ) {
            $authResponse = $this->authService
                ->changePassword(
                    $this->authService->getCurrentUID(),
                    $data['currPassword'],
                    $data['password'],
                    $data['password']
                );

            if (
                isset($authResponse['error']) &&
                $authResponse['error']
            ) {
                $errorMessage = isset($authResponse['message']) ? $authResponse['message'] : '';

                // redirect in case of error
                return $response
                    ->withRedirect(
                        $this->routerHelper->getHotelOwnerAdminAccountSettingsUrl() . '?errorMessage=' . $errorMessage,
                        301
                    );
            } else {
                return $response
                    ->withRedirect(
                        $this->routerHelper->getHotelOwnerAdminAccountSettingsUrl() . '?successMessage=Password have been successfully changed.',
                        301
                    );
            }
        }

        // change email

        if (
            isset($data['email']) && isset($data['currPassword']) &&
            $data['email'] != $this->hotelOwnerUser->getEmail()
        ) {
            $authResponse = $this->authService
                ->changeEmail(
                    $this->authService->getCurrentUID(),
                    $data['email'],
                    $data['currPassword']
                );

            if (
                isset($authResponse['error']) &&
                $authResponse['error']
            ) {
                $errorMessage = isset($authResponse['message']) ? $authResponse['message'] : '';

                // redirect in case of error
                return $response
                    ->withRedirect(
                        $this->routerHelper->getHotelOwnerAdminAccountSettingsUrl() . '?errorMessage=' . $errorMessage,
                        301
                    );
            } else {
                return $response
                    ->withRedirect(
                        $this->routerHelper->getHotelOwnerAdminAccountSettingsUrl() . '?successMessage=Email have been successfully changed.',
                        301
                    );
            }
        }

        return $response
            ->withRedirect(
                $this->routerHelper->getHotelOwnerAdminAccountSettingsUrl() . '?successMessage=Invalid request.',
                301
            );
    }

}