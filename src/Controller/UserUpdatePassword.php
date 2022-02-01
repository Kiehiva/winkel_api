<?php

namespace App\Controller;

use App\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UserUpdatePassword extends AbstractController
{
    public function __invoke(Customer $data)
    {
        return $data;
    }
}
