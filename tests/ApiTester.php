<?php
namespace Tests;

use Faker\Factory as Faker;
use PHPUnit\Framework\Assert;

class ApiTester extends TestCase {

	protected $fake;

	protected $times = 1;

	public function __construct(?string $name = null, array $data = [], string $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
		$this->fake = Faker::create();
	}

	protected function times($count)
	{
		$this->times = $count;

		return $this;
	}

	protected function assertObjectHasAttributes()
	{
		$arguments = collect(func_get_args());
		$object = $arguments->pull(0);

		$arguments->each(function($attribute) use ($object) {
Assert::assertObjectHasAttribute($attribute, $object);
		});
	}
}
