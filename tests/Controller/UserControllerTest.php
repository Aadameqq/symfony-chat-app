<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{

    public function testCreate_WhenUserWithGivenUsernameAlreadyExists_ShouldReturnConflictAndNotCreateNewUserInDb(): void
    {
        $client = static::createClient();
        $testUsername = "username";
        $testUser = new User();
        $testUser->setUsername($testUsername);
        $testUser->setPassword("pwd");
        $entityManager = $this->getEntityManager();
        $entityManager->persist($testUser);
        $entityManager->flush();
        $userRepo = $this->getUserRepository();

        $client->jsonRequest('POST','/user', parameters: [
            "username"=>$testUsername,
            "plainPassword"=>"validpassword"
        ]);

        $actualUsersCount = $userRepo->count();
        $this->assertResponseStatusCodeSame(409);
        $this->assertSame(1,$actualUsersCount);
    }


    public function testCreate_CreatesUser()
    {
        $client = static::createClient();
        $testUsername = "username";

        $client->jsonRequest('POST','/user', parameters: [
            "username"=>$testUsername,
            "plainPassword"=>"validpassword"
        ]);

        $this->assertResponseStatusCodeSame(201);
    }

    private static function getEntityManager():EntityManagerInterface{
        return static::getContainer()->get(EntityManagerInterface::class);
    }
    private static function getUserRepository():UserRepository{
        return static::getContainer()->get(UserRepository::class);
    }

}
