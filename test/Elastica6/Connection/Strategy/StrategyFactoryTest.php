<?php
namespace Elastica6\Test\Connection\Strategy;

use Elastica6\Connection\Strategy\CallbackStrategy;
use Elastica6\Connection\Strategy\Simple;
use Elastica6\Connection\Strategy\StrategyFactory;
use Elastica6\Test\Base;

/**
 * Description of StrategyFactoryTest.
 *
 * @author chabior
 */
class StrategyFactoryTest extends Base
{
    /**
     * @group unit
     */
    public function testCreateCallbackStrategy()
    {
        $callback = function ($connections) {
        };

        $strategy = StrategyFactory::create($callback);

        $this->assertInstanceOf(CallbackStrategy::class, $strategy);
    }

    /**
     * @group unit
     */
    public function testCreateByName()
    {
        $strategyName = 'Simple';

        $strategy = StrategyFactory::create($strategyName);

        $this->assertInstanceOf(Simple::class, $strategy);
    }

    /**
     * @group unit
     */
    public function testCreateByClass()
    {
        $strategy = new EmptyStrategy();

        $this->assertEquals($strategy, StrategyFactory::create($strategy));
    }

    /**
     * @group unit
     */
    public function testCreateByClassName()
    {
        $strategy = StrategyFactory::create(EmptyStrategy::class);

        $this->assertInstanceOf(EmptyStrategy::class, $strategy);
    }

    /**
     * @group unit
     * @expectedException \InvalidArgumentException
     */
    public function testFailCreate()
    {
        $strategy = new \stdClass();

        StrategyFactory::create($strategy);
    }

    /**
     * @group unit
     */
    public function testNoCollisionWithGlobalNamespace()
    {
        // create collision
        if (!class_exists('Simple')) {
            class_alias('Elastica\Util', 'Simple');
        }
        $strategy = StrategyFactory::create('Simple');
        $this->assertInstanceOf(Simple::class, $strategy);
    }
}
