<?php
declare (strict_types = 1);

use App\libraries\buildings\Queue;
use App\libraries\buildings\QueueElements;
use PHPUnit\Framework\TestCase;

/**
 * @covers Queue
 */
class QueueTest extends TestCase
{
    /**
     * @covers App\libraries\buildings\Queue::addElementToQueue
     */
    public function testAddOneElementToQueue(): void
    {
        $object = new Queue();
        $current_time = time();

        $queue_elements = new QueueElements;
        $queue_elements->building = 1;
        $queue_elements->build_level = 1;
        $queue_elements->build_time = 20;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);

        $this->assertEquals(
            $object->returnQueueAsString(), '1,1,20,' . $current_time . ',build'
        );
    }

    /**
     * @covers App\libraries\buildings\Queue::addElementToQueue
     */
    public function testAddManyElementToQueue(): void
    {
        $object = new Queue();
        $current_time = time();

        $queue_elements = new QueueElements;
        $queue_elements->building = 1;
        $queue_elements->build_level = 1;
        $queue_elements->build_time = 20;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);

        // add second element
        $queue_elements = new QueueElements;
        $queue_elements->building = 2;
        $queue_elements->build_level = 5;
        $queue_elements->build_time = 90;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'destroy';

        $object->addElementToQueue($queue_elements);

        $this->assertEquals(
            $object->returnQueueAsString(), '1,1,20,' . $current_time . ',build;2,5,90,' . $current_time . ',destroy'
        );

        // add third element
        $queue_elements = new QueueElements;
        $queue_elements->building = 3;
        $queue_elements->build_level = 10;
        $queue_elements->build_time = 120;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);

        $this->assertEquals(
            $object->returnQueueAsString(), '1,1,20,' . $current_time . ',build;2,5,90,' . $current_time . ',destroy;3,10,120,' . $current_time . ',build'
        );
    }

    /**
     * @covers App\libraries\buildings\Queue::removeElementFromQueue
     */
    public function testRemoveElementFromQueueWithOneElement(): void
    {
        // add one element
        $object = new Queue();
        $current_time = time();

        $queue_elements = new QueueElements;
        $queue_elements->building = 1;
        $queue_elements->build_level = 1;
        $queue_elements->build_time = 20;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);
        $object->removeElementFromQueue(0);

        $this->assertEquals(
            $object->returnQueueAsString(), ''
        );
    }

    /**
     * @covers App\libraries\buildings\Queue::removeElementFromQueue
     */
    public function testRemoveLastElementFromQueueWithTwoElement(): void
    {
        $object = new Queue();
        $current_time = time();

        // add first element
        $queue_elements = new QueueElements;
        $queue_elements->building = 1;
        $queue_elements->build_level = 1;
        $queue_elements->build_time = 20;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);

        // add second element
        $queue_elements = new QueueElements;
        $queue_elements->building = 2;
        $queue_elements->build_level = 5;
        $queue_elements->build_time = 90;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'destroy';

        $object->addElementToQueue($queue_elements);

        $object->removeElementFromQueue(1);

        $this->assertEquals(
            $object->returnQueueAsString(), '1,1,20,' . $current_time . ',build'
        );
    }

    /**
     * @covers App\libraries\buildings\Queue::removeElementFromQueue
     */
    public function testRemoveFirstElementFromQueueWithTwoElement(): void
    {
        $object = new Queue();
        $current_time = time();

        // add first element
        $queue_elements = new QueueElements;
        $queue_elements->building = 1;
        $queue_elements->build_level = 1;
        $queue_elements->build_time = 20;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);

        // add second element
        $queue_elements = new QueueElements;
        $queue_elements->building = 2;
        $queue_elements->build_level = 5;
        $queue_elements->build_time = 90;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'destroy';

        $object->addElementToQueue($queue_elements);

        $object->removeElementFromQueue(0);

        $this->assertEquals(
            $object->returnQueueAsString(), '2,5,90,' . $current_time . ',destroy'
        );
    }

    /**
     * @covers App\libraries\buildings\Queue::removeElementFromQueue
     */
    public function testRemoveMiddleElementFromQueueWithThreeElement(): void
    {
        $object = new Queue();
        $current_time = time();

        // add first element
        $queue_elements = new QueueElements;
        $queue_elements->building = 1;
        $queue_elements->build_level = 1;
        $queue_elements->build_time = 20;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);

        // add second element
        $queue_elements = new QueueElements;
        $queue_elements->building = 2;
        $queue_elements->build_level = 5;
        $queue_elements->build_time = 90;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'destroy';

        $object->addElementToQueue($queue_elements);

        // add third element
        $queue_elements = new QueueElements;
        $queue_elements->building = 3;
        $queue_elements->build_level = 10;
        $queue_elements->build_time = 120;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);

        $object->removeElementFromQueue(1);

        $this->assertEquals(
            $object->returnQueueAsString(), '1,1,20,' . $current_time . ',build;3,10,120,' . $current_time . ',build'
        );
    }

    /**
     * @covers App\libraries\buildings\Queue::removeElementFromQueue
     */
    public function testRemoveElementFromQueueInvalidParameters(): void
    {
        $object = new Queue();
        $current_time = time();

        // add first element
        $queue_elements = new QueueElements;
        $queue_elements->building = 1;
        $queue_elements->build_level = 1;
        $queue_elements->build_time = 20;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);

        // add second element
        $queue_elements = new QueueElements;
        $queue_elements->building = 2;
        $queue_elements->build_level = 5;
        $queue_elements->build_time = 90;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'destroy';

        $object->addElementToQueue($queue_elements);

        // add third element
        $queue_elements = new QueueElements;
        $queue_elements->building = 3;
        $queue_elements->build_level = 10;
        $queue_elements->build_time = 120;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);

        $object->removeElementFromQueue("wrong_parameter");

        $this->assertEquals(
            $object->returnQueueAsString(), '1,1,20,' . $current_time . ',build;2,5,90,' . $current_time . ',destroy;3,10,120,' . $current_time . ',build'
        );
    }

    /**
     * @covers App\libraries\buildings\Queue::getElementFromQueueAsArray
     */
    public function testGetElementFromQueueAsArray(): void
    {
        $object = new Queue();
        $current_time = time();

        // add first element
        $queue_elements = new QueueElements;
        $queue_elements->building = 1;
        $queue_elements->build_level = 1;
        $queue_elements->build_time = 20;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);

        // add second element
        $queue_elements = new QueueElements;
        $queue_elements->building = 2;
        $queue_elements->build_level = 5;
        $queue_elements->build_time = 90;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'destroy';

        $object->addElementToQueue($queue_elements);

        // add third element
        $queue_elements = new QueueElements;
        $queue_elements->building = 3;
        $queue_elements->build_level = 10;
        $queue_elements->build_time = 120;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);

        $this->assertEquals(
            $object->getElementFromQueueAsArray(1), [
                'building' => 2,
                'build_level' => 5,
                'build_time' => 90,
                'build_end_time' => $current_time,
                'build_mode' => 'destroy',
            ]
        );
    }

    /**
     * @covers App\libraries\buildings\Queue::returnQueueAsString
     */
    public function testReturnQueueAsString(): void
    {
        $object = new Queue();
        $current_time = time();

        // add first element
        $queue_elements = new QueueElements;
        $queue_elements->building = 1;
        $queue_elements->build_level = 1;
        $queue_elements->build_time = 20;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);

        // Remove the following lines when you implement this test.
        $this->assertIsString(
            $object->returnQueueAsString()
        );

        $this->assertEquals(
            $object->returnQueueAsString(), '1,1,20,' . $current_time . ',build'
        );
    }

    /**
     * @covers App\libraries\buildings\Queue::returnQueueAsArray
     */
    public function testReturnQueueAsArray(): void
    {
        $object = new Queue();
        $current_time = time();

        // add first element
        $queue_elements = new QueueElements;
        $queue_elements->building = 1;
        $queue_elements->build_level = 1;
        $queue_elements->build_time = 20;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);

        // Remove the following lines when you implement this test.
        $this->assertIsArray(
            $object->returnQueueAsArray()
        );

        $this->assertEquals(
            $object->returnQueueAsArray(), [
                0 =>
                [
                    'building' => 1,
                    'build_level' => 1,
                    'build_time' => 20,
                    'build_end_time' => $current_time,
                    'build_mode' => 'build',
                ],
            ]
        );
    }

    /**
     * @covers App\libraries\buildings\Queue::countQueueElements
     */
    public function testCountQueueElements(): void
    {
        $object = new Queue();
        $current_time = time();

        // add first element
        $queue_elements = new QueueElements;
        $queue_elements->building = 1;
        $queue_elements->build_level = 1;
        $queue_elements->build_time = 20;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);

        // add second element
        $queue_elements = new QueueElements;
        $queue_elements->building = 2;
        $queue_elements->build_level = 5;
        $queue_elements->build_time = 90;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'destroy';

        $object->addElementToQueue($queue_elements);

        // add third element
        $queue_elements = new QueueElements;
        $queue_elements->building = 3;
        $queue_elements->build_level = 10;
        $queue_elements->build_time = 120;
        $queue_elements->build_end_time = $current_time;
        $queue_elements->build_mode = 'build';

        $object->addElementToQueue($queue_elements);

        // Remove the following lines when you implement this test.
        $this->assertIsArray(
            $object->returnQueueAsArray()
        );

        $this->assertEquals(
            $object->countQueueElements(), 3
        );
    }
}
