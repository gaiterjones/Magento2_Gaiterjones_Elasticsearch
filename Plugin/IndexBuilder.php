<?php

declare(strict_types=1);

namespace Gaiterjones\Elasticsearch\Plugin;

class IndexBuilder {

    // Improve SKU search by increasing ngram value in elastic search index
    // https://magento.stackexchange.com/questions/307324/magento-2-elastic-search-6-fulltext-search-with-like
    //
    //
    //
    public function afterBuild(\Magento\Elasticsearch\Model\Adapter\Index\Builder $subject, $result)
    {
        $likeToken = $this->getLikeTokenizer();
        $result['analysis']['tokenizer'] = $likeToken;
        $result['analysis']['filter']['trigrams_filter'] = [
            'type' => 'ngram',
            'min_gram' => 3,
            'max_gram' => 3
        ];
        $result['analysis']['analyzer']['my_analyzer'] = [
            'type' => 'custom',
            'tokenizer' => 'standard',
            'filter' => [
                'lowercase', 'trigrams_filter'
            ]
        ];
        return $result;
    }


    protected function getLikeTokenizer()
    {
        $tokenizer = [
            'default_tokenizer' => [
                'type' => 'ngram'
            ],
        ];
        return $tokenizer;
    }
}
