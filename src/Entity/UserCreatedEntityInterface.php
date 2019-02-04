<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-02-03
 * Time: 13:22
 */

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserCreatedEntityInterface
{
    public function setUser(UserInterface $user): UserCreatedEntityInterface;
}