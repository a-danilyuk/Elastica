<?php
namespace Elastica6\Query;

use Elastica6\Document;

/**
 * More Like This query.
 *
 * @author Raul Martinez, Jr <juneym@gmail.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-mlt-query.html
 */
class MoreLikeThis extends AbstractQuery
{
    /**
     * Set fields to which to restrict the mlt query.
     *
     * @param array $fields Field names
     *
     * @return \Elastica6\Query\MoreLikeThis Current object
     */
    public function setFields(array $fields)
    {
        return $this->setParam('fields', $fields);
    }

    /**
     * Set the "like" value.
     *
     * @param string|Document $like
     *
     * @return $this
     */
    public function setLike($like)
    {
        return $this->setParam('like', $like);
    }

    /**
     * Set boost.
     *
     * @param float $boost Boost value
     *
     * @return $this
     */
    public function setBoost($boost)
    {
        return $this->setParam('boost', (float) $boost);
    }

    /**
     * Set max_query_terms.
     *
     * @param int $maxQueryTerms Max query terms value
     *
     * @return $this
     */
    public function setMaxQueryTerms($maxQueryTerms)
    {
        return $this->setParam('max_query_terms', (int) $maxQueryTerms);
    }

    /**
     * Set min term frequency.
     *
     * @param int $minTermFreq
     *
     * @return $this
     */
    public function setMinTermFrequency($minTermFreq)
    {
        return $this->setParam('min_term_freq', (int) $minTermFreq);
    }

    /**
     * set min document frequency.
     *
     * @param int $minDocFreq
     *
     * @return $this
     */
    public function setMinDocFrequency($minDocFreq)
    {
        return $this->setParam('min_doc_freq', (int) $minDocFreq);
    }

    /**
     * set max document frequency.
     *
     * @param int $maxDocFreq
     *
     * @return $this
     */
    public function setMaxDocFrequency($maxDocFreq)
    {
        return $this->setParam('max_doc_freq', (int) $maxDocFreq);
    }

    /**
     * Set min word length.
     *
     * @param int $minWordLength
     *
     * @return $this
     */
    public function setMinWordLength($minWordLength)
    {
        return $this->setParam('min_word_length', (int) $minWordLength);
    }

    /**
     * Set max word length.
     *
     * @param int $maxWordLength
     *
     * @return $this
     */
    public function setMaxWordLength($maxWordLength)
    {
        return $this->setParam('max_word_length', (int) $maxWordLength);
    }

    /**
     * Set boost terms.
     *
     * @param bool $boostTerms
     *
     * @return $this
     */
    public function setBoostTerms($boostTerms)
    {
        return $this->setParam('boost_terms', (bool) $boostTerms);
    }

    /**
     * Set analyzer.
     *
     * @param string $analyzer
     *
     * @return $this
     */
    public function setAnalyzer($analyzer)
    {
        $analyzer = trim($analyzer);

        return $this->setParam('analyzer', $analyzer);
    }

    /**
     * Set stop words.
     *
     * @param array $stopWords
     *
     * @return $this
     */
    public function setStopWords(array $stopWords)
    {
        return $this->setParam('stop_words', $stopWords);
    }

    /**
     * Set minimum_should_match option.
     *
     * @param int|string $minimumShouldMatch
     *
     * @return $this
     */
    public function setMinimumShouldMatch($minimumShouldMatch)
    {
        return $this->setParam('minimum_should_match', $minimumShouldMatch);
    }

    public function toArray()
    {
        $array = parent::toArray();

        // If _id is provided, perform MLT on an existing document from the index
        // If _source is provided, perform MLT on a document provided as an input
        if (!empty($array['more_like_this']['like']['_id'])) {
            $doc = $array['more_like_this']['like'];
            $doc = array_intersect_key($doc, ['_index' => 1, '_type' => 1, '_id' => 1]);
            $array['more_like_this']['like'] = $doc;
        } elseif (!empty($array['more_like_this']['like']['_source'])) {
            $doc = $array['more_like_this']['like'];
            $doc['doc'] = $array['more_like_this']['like']['_source'];
            unset($doc['_id']);
            unset($doc['_source']);
            $array['more_like_this']['like'] = $doc;
        }

        return $array;
    }
}
