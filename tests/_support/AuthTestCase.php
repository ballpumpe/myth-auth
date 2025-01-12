<?php namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\Fabricator;
use Faker\Factory;
use Myth\Auth\Authorization\GroupModel;
use Myth\Auth\Authorization\PermissionModel;
use Myth\Auth\Models\UserModel;
use Myth\Auth\Test\AuthTestTrait;
use Myth\Auth\Test\Fakers\GroupFaker;
use Myth\Auth\Test\Fakers\PermissionFaker;
use Myth\Auth\Test\Fakers\UserFaker;

class AuthTestCase extends CIUnitTestCase
{
	use AuthTestTrait;
	use DatabaseTestTrait;

	/**
	 * Should the database be refreshed before each test?
	 *
	 * @var boolean
	 */
	protected $refresh = true;

	/**
	 * The name of a seed file used for all tests within this test case.
	 *
	 * @var string
	 */
	protected $seed = 'Tests\Support\Database\Seeds\AuthSeeder';

	/**
	 * The path to where we can find the test Seeds directory.
	 *
	 * @var string
	 */
	protected $basePath = SUPPORTPATH . 'Database/';

	/**
	 * The namespace to help us find the migration classes.
	 *
	 * @var string
	 */
	protected $namespace = 'Myth\Auth';

	/**
	 * @var \Myth\Auth\Models\UserModel
	 */
	protected $users;

    /**
     * @var GroupModel
     */
	protected $groups;

    /**
     * @var PermissionModel
     */
	protected $permissions;

	/**
	 * @var Faker\Generator
	 */
	protected $faker;

	/**
	 * @var SessionHandler
	 */
	protected $session;

	public function setUp(): void
	{
		parent::setUp();
		
		$this->resetAuthServices();

		$this->users       = model(UserModel::class, false);
		$this->groups      = model(GroupModel::class, false);
		$this->permissions = model(PermissionModel::class, false);
	
		$this->faker = Factory::create();
	}

	/**
	 * Creates a user on-the-fly.
	 *
	 * @param string $reason
	 *
	 * @return $this
	 */
	protected function createUser(array $info = [])
	{
		$defaults = [
			'email'            => 'fred@example.com',
			'username'         => 'Fred',
			'password'         => 'secret',
			'force_pass_reset' => false,
		];
		$info = array_merge($defaults, $info);

		$fabricator = new Fabricator(UserFaker::class);
		$fabricator->setOverrides($info, false);

		$user = $fabricator->create();

		// Delete any cached permissions
        cache()->delete($user->id . '_permissions');

		return $user;
	}

    /**
     * Creates a group on the fly
     *
     * @param array $info
     *
     * @return mixed
     */
	protected function createGroup(array $info = [])
    {
		$fabricator = new Fabricator(GroupFaker::class);
		$fabricator->setOverrides($info, false);

        return $fabricator->create();
    }

    /**
     * Creates a new permission on the fly.
     *
     * @param array $info
     *
     * @return mixed
     */
    protected function createPermission(array $info = [])
    {
		$fabricator = new Fabricator(PermissionFaker::class);
		$fabricator->setOverrides($info, false);

        return (object) $fabricator->create();
    }
}
