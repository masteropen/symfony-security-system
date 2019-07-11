<?php

namespace App\Service\Fixture;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FakerProvider
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * FakerProvider constructor.
     *
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @param string $password
     *
     * @return string
     */
    public function hashUserPassword($password)
    {
        return password_hash($password, 1);
    }
}
