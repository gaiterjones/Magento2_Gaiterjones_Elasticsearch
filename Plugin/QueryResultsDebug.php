<?php

declare(strict_types=1);

namespace Gaiterjones\Elasticsearch\Plugin;

use Magento\Elasticsearch\SearchAdapter;
use Gaiterjones\Elasticsearch\Model\Configuration;
use Psr\Log\LoggerInterface;


class QueryResultsDebug
{
    /**
     * @var logger
     */
    protected $logger;
    /**
     * @var Configuration
     */
    private $configuration;
    /**
     * @var debug
     */
     private $debug;

    public function __construct(
        Configuration $configuration,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->debug=true;

    }

    //
    // tail -F debug.log | grep ELASTIC_SEARCH_QUERY_RESULTS_DEBUG
    //
    public function beforeCreate(\Magento\Elasticsearch\SearchAdapter\ResponseFactory $subject, $result)
    {
       if($this->debug)
       {
           if(!is_array($result) || empty($result)) {return false;}

           $scores=array();

           foreach ($result['documents'] as $rawDocument) {
               $this->logger->debug('ELASTIC_SEARCH_QUERY_RESULTS_DEBUG',$rawDocument);
               array_push($scores,$rawDocument['_score']);
           }

           if (count($result['documents']) > 0)
           {
              $_debug=array(
                   'results' => count($result['documents']),
                   'scores' => $scores,
                   'min_relevance_score' => $this->configuration->getMinScore(),
                   'min_score' => min($scores),
                   'max_score' => max($scores)
               );

               $this->logger->debug('ELASTIC_SEARCH_QUERY_RESULTS_DEBUG',$_debug);
           }
       }
    }

}
