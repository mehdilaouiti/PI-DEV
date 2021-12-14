<?php

namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\DateTime;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');
        $roles[] = 'ROLE_ADMIN' ;
        $adminUser = new User();
        $adminUser->setName('majdi')
            ->setEmail('ma@gmail.Com')
            ->setPassword($this->encoder->encodePassword($adminUser, '111111'))
            ->setCin(125478963)
            ->setRoles($roles)
            ->setPhone(258965418)
            ->setGenre("Homme");
        $manager->persist($adminUser);





        $role[] = 'ROLE_USER' ;
        $users = [];
        $genres = ['Homme', 'Femme'];
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();


            $genre = $faker->randomElement($genres);

            $hash = $this->encoder->encodePassword($user, 'password');
            $user->setName($faker->name($genre))
                ->setEmail($faker->email)
                ->setCin($faker->regexify('^[0-9]{8}$'))
                ->setGenre($genre)
                ->setPhone($faker->regexify('^[0-9]{8}$'))
                ->setPassword($hash)
                ->setRoles($role);
            if($genre=='Femme'){
                $user->setGenre('Femme');
                $manager->persist($user);
            }
            else{
                $user->setGenre("Homme");
                $manager->persist($user);
            }


            $manager->flush();
        }
    }
}
