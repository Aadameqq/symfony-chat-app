<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{

    public function testCreate_WhenUserWithGivenUsernameAlreadyExists_ShouldReturnConflictHttpStatusAndNotCreateNewUserInDb(): void
    {
        $client = static::createClient();
        $username = "username";
        $user = new User();
        $user->setUsername($username);
        $user->setPassword("password");
        $entityManager = static::getEntityManager();
        $entityManager->persist($user);
        $entityManager->flush();
        $userRepo = static::getUserRepository();

        $client->jsonRequest('POST','/user', parameters: [
            "username"=>$username,
            "plainPassword"=>"password123456"
        ]);

        $actualUsersCount = $userRepo->count();
        $this->assertResponseStatusCodeSame(409);
        $this->assertSame(1,$actualUsersCount);
    }


    public function testCreate_ShouldCreateUserInDbAndReturnCreatedHttpStatus()
    {
        $client = static::createClient();
        $username = "username";
        $userRepo = static::getUserRepository();


        $client->jsonRequest('POST','/user', parameters: [
            "username"=>$username,
            "plainPassword"=>"validpassword"
        ]);

        $actualUser = $userRepo->findOneBy(["username"=>$username]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertNotNull($actualUser);
    }

    private static function getEntityManager():EntityManagerInterface{
        return static::getContainer()->get(EntityManagerInterface::class);
    }
    private static function getUserRepository():UserRepository{
        return static::getContainer()->get(UserRepository::class);
    }

}
