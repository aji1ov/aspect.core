<?php

namespace Aspect\Lib\Service\Background;

use Aspect\Lib\Application;
use Closure;
use Laravel\SerializableClosure\SerializableClosure;
use ReflectionException;

class ClosureJob extends Job
{
    private string $name;

    private SerializableClosure $closure;
    public function __construct(Closure $closure)
    {
        $this->closure = new SerializableClosure($closure);
    }

    /**
     * @throws ReflectionException
     */
    public function handle(): void
    {
        Application::getInstance()->inject($this->closure->getClosure(), ['job' => $this]);
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name ?? parent::getName();
    }


}