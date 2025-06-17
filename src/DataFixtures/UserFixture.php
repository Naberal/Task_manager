<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\User\Domain\Entities\User;
use App\User\Domain\VO\ApiKey;
use App\User\Domain\VO\UserId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public const USER_1_ID = '12345678';
    public const USER_1_API_KEY = '1234567890123456';

    public const USER_2_ID = '87654321';
    public const USER_2_API_KEY = '6543210987654321';

    public const USER_3_ID = '11111111';
    public const USER_3_API_KEY = '1111111111111111';

    public const USER_4_ID = '22222222';
    public const USER_4_API_KEY = '2222222222222222';

    public function load(ObjectManager $manager): void
    {
        $user1 = new User(
            new UserId(self::USER_1_ID),
            new ApiKey(self::USER_1_API_KEY)
        );
        $manager->persist($user1);
        $this->addReference('user-1', $user1);

        $user2 = new User(
            new UserId(self::USER_2_ID),
            new ApiKey(self::USER_2_API_KEY)
        );
        $manager->persist($user2);
        $this->addReference('user-2', $user2);

        $user3 = new User(
            new UserId(self::USER_3_ID),
            new ApiKey(self::USER_3_API_KEY)
        );
        $manager->persist($user3);
        $this->addReference('user-3', $user3);

        $user4 = new User(
            new UserId(self::USER_4_ID),
            new ApiKey(self::USER_4_API_KEY)
        );
        $manager->persist($user4);
        $this->addReference('user-4', $user4);

        $manager->flush();
    }
}
