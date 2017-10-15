<?php

namespace Tests\Utils\Processor;

use Broadway\CommandHandling\Testing\TraceableCommandBus;
use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use Broadway\Domain\Metadata;

/**
 * Class Scenario
 */
class Scenario
{
    protected $processor;
    protected $playhead;
    protected $traceableCommandBus;
    protected $testCase;

    /**
     * Scenario constructor.
     *
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param TraceableCommandBus         $traceableCommandBus
     * @param EventListener      $processor
     */
    public function __construct(
        \PHPUnit_Framework_TestCase $testCase,
        TraceableCommandBus $traceableCommandBus,
        EventListener $processor
    ) {
        $this->testCase = $testCase;
        $this->traceableCommandBus = $traceableCommandBus;
        $this->processor = $processor;
        $this->playhead = -1;
    }

    /**
     * @param array $events
     *
     * @return Scenario
     */
    public function given(array $events = [])
    {
        foreach ($events as $given) {
            $this->processor->handle($this->createDomainMessageForEvent($given));
        }

        return $this;
    }

    /**
     * @param mixed $event
     *
     * @return Scenario
     */
    public function when($event)
    {
        $this->traceableCommandBus->record();

        $this->processor->handle($this->createDomainMessageForEvent($event));

        return $this;
    }

    /**
     * @param array $commands
     *
     * @return Scenario
     */
    public function then(array $commands)
    {
        $this->testCase->assertEquals($commands, $this->traceableCommandBus->getRecordedCommands());

        return $this;
    }

    /**
     * @param $event
     *
     * @return DomainMessage
     */
    private function createDomainMessageForEvent($event)
    {
        $this->playhead++;

        return DomainMessage::recordNow(1, $this->playhead, new Metadata([]), $event);
    }
}
