<?php
/**
 * Connection class file.
 * @author Petra Barus <petra.barus@gmail.com>
 */

namespace UrbanIndo\Yii2\DynamoDb;

use Yii;
use Aws\DynamoDb\DynamoDbClient;

/**
 * Connection wraps DynamoDB connection for Aws PHP SDK.
 *
 * @author Petra Barus <petra.barus@gmail.com>
 * @see DynamoDbClient
 */
class Connection extends \yii\base\Component
{

    /**
     * The configuration for DynamoDB client.
     * @var array
     */
    public $config;

    /**
     * The DynamoDB client.
     * @var DynamoDbClient
     */
    protected $_client;

    /**
     * The query builder.
     * @var QueryBuilder
     */
    protected $_builder;

    /**
     * Initialize the DynamoDB client.
     * @return void
     */
    public function init()
    {
        parent::init();

        if (empty($this->config['credentials'])) { //using IAM Role
            $provider = CredentialProvider::instanceProfile();
            $memoizedProvider = CredentialProvider::memoize($provider);          // Be sure to memoize the credentials
            $this->config['credentials'] = $memoizedProvider;
        }

        $this->_client = new DynamoDbClient($this->config);
    }

    /**
     * @return DynamoDbClient
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Creates a command for execution.
     * @param array $config The configuration for the Command class.
     * @return Command the DB command
     */
    public function createCommand(array $config = [])
    {
        $command = Yii::createObject(array_merge($config, [
            'class' => Command::className(),
            'db' => $this
        ]));
        return $command;
    }

    /**
     * Returns the query builder for this connection.
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        if ($this->_builder === null) {
            $this->_builder = new QueryBuilder($this);
        }
        return $this->_builder;
    }
}
