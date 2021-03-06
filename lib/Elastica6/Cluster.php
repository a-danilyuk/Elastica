<?php
namespace Elastica6;

use Elastica6\Cluster\Health;
use Elastica6\Cluster\Settings;
use Elastica6\Exception\NotImplementedException;
use Elasticsearch\Endpoints\Cluster\State;

/**
 * Cluster information for elasticsearch.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/cluster.html
 */
class Cluster
{
    /**
     * Client.
     *
     * @var \Elastica6\Client Client object
     */
    protected $_client;

    /**
     * Cluster state response.
     *
     * @var \Elastica6\Response
     */
    protected $_response;

    /**
     * Cluster state data.
     *
     * @var array
     */
    protected $_data;

    /**
     * Creates a cluster object.
     *
     * @param \Elastica6\Client $client Connection client object
     */
    public function __construct(Client $client)
    {
        $this->_client = $client;
        $this->refresh();
    }

    /**
     * Refreshes all cluster information (state).
     */
    public function refresh()
    {
        $this->_response = $this->_client->requestEndpoint(new State());
        $this->_data = $this->getResponse()->getData();
    }

    /**
     * Returns the response object.
     *
     * @return \Elastica6\Response Response object
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Return list of index names.
     *
     * @return array List of index names
     */
    public function getIndexNames()
    {
        return array_keys($this->_data['metadata']['indices']);
    }

    /**
     * Returns the full state of the cluster.
     *
     * @return array State array
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/cluster-state.html
     */
    public function getState()
    {
        return $this->_data;
    }

    /**
     * Returns a list of existing node names.
     *
     * @return array List of node names
     */
    public function getNodeNames()
    {
        $data = $this->getState();
        $nodeNames = [];
        foreach ($data['nodes'] as $node) {
            $nodeNames[] = $node['name'];
        }

        return $nodeNames;
    }

    /**
     * Returns all nodes of the cluster.
     *
     * @return \Elastica6\Node[]
     */
    public function getNodes()
    {
        $nodes = [];
        $data = $this->getState();

        foreach ($data['nodes'] as $id => $name) {
            $nodes[] = new Node($id, $this->getClient());
        }

        return $nodes;
    }

    /**
     * Returns the client object.
     *
     * @return \Elastica6\Client Client object
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Returns the cluster information (not implemented yet).
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/cluster-nodes-info.html
     *
     * @param array $args Additional arguments
     *
     * @throws \Elastica6\Exception\NotImplementedException
     */
    public function getInfo(array $args)
    {
        throw new NotImplementedException('not implemented yet');
    }

    /**
     * Return Cluster health.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/cluster-health.html
     *
     * @return \Elastica6\Cluster\Health
     */
    public function getHealth()
    {
        return new Health($this->getClient());
    }

    /**
     * Return Cluster settings.
     *
     * @return \Elastica6\Cluster\Settings
     */
    public function getSettings()
    {
        return new Settings($this->getClient());
    }
}
