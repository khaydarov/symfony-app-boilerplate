<?php

declare(strict_types=1);

namespace App\Auth;

use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
//        $userData = $this->jwtTokenManager->parse($accessToken);
//        $username = $userData['username'];

        return new UserBadge(
            "Me",
            function (string $username) {
//                return $this->userRepository->findOneBy(['username' => $username]);
                return ['password' => 'password'];
            }
        );
    }
}