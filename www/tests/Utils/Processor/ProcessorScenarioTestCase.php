<?php

namespace Tests\Utils\Processor;

use Broadway\CommandHandling\Testing\TraceableCommandBus;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class ProcessorScenarioTestCase
 */
abstract class ProcessorScenarioTestCase extends TestCase
{

    /**
     * @var Scenario
     */
    protected $scenario;

    public function setUp()
    {
        $this->scenario = $this->createScenario();
    }

    /**
     * @return Scenario
     */
    protected function createScenario()
    {
        $traceableCommandBus = new TraceableCommandBus();
        $process = $this->createProcessor($traceableCommandBus);

        return new Scenario($this, $traceableCommandBus, $process);
    }

    /**
     * @param TraceableCommandBus $traceableCommandBus
     *
     * @return mixed
     */
    abstract protected function createProcessor(TraceableCommandBus $traceableCommandBus);
}
