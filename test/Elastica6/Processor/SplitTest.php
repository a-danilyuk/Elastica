<?php
namespace Elastica6\Test\Processor;

use Elastica6\Bulk;
use Elastica6\Document;
use Elastica6\Processor\Split;
use Elastica6\ResultSet;
use Elastica6\Test\BasePipeline as BasePipelineTest;

class SplitTest extends BasePipelineTest
{
    /**
     * @group unit
     */
    public function testSplit()
    {
        $processor = new Split('joined_array_field', '-');

        $expected = [
            'split' => [
                'field' => 'joined_array_field',
                'separator' => '-',
            ],
        ];

        $this->assertEquals($expected, $processor->toArray());
    }

    /**
     * @group unit
     */
    public function testSplitWithNonDefaultOptions()
    {
        $processor = new Split('joined_array_field', '-');
        $processor->setIgnoreMissing(true);

        $expected = [
            'split' => [
                'field' => 'joined_array_field',
                'separator' => '-',
                'ignore_missing' => true,
            ],
        ];

        $this->assertEquals($expected, $processor->toArray());
    }

    /**
     * @group functional
     */
    public function testSplitField()
    {
        $split = new Split('name', '&');

        $pipeline = $this->_createPipeline('my_custom_pipeline', 'pipeline for Split');
        $pipeline->addProcessor($split)->create();

        $index = $this->_createIndex();
        $type = $index->getType('bulk_test');

        // Add document to normal index
        $doc1 = new Document(null, ['name' => 'nicolas&ruflin']);

        $bulk = new Bulk($index->getClient());
        $bulk->setIndex($index);
        $bulk->setType($type);

        $bulk->addDocuments([
            $doc1,
        ]);
        $bulk->setRequestParam('pipeline', 'my_custom_pipeline');

        $bulk->send();
        $index->refresh();

        /** @var ResultSet $result */
        $result = $index->search('*');

        $this->assertEquals(1, count($result->getResults()));

        $results = $result->getResults();
        $this->assertSame(['nicolas', 'ruflin'], ($results[0]->getHit())['_source']['name']);
    }
}
