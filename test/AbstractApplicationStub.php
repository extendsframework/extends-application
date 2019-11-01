<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

class AbstractApplicationStub extends AbstractApplication
{
    /**
     * @var bool
     */
    protected $called = false;

    /**
     * @return bool
     */
    public function isCalled(): bool
    {
        return $this->called;
    }

    /**
     * @inheritDoc
     */
    protected function run(): AbstractApplication
    {
        $this->called = true;

        return $this;
    }
}
