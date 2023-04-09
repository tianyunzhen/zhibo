<?php

class Elast_GiftRecord extends Elast_Base{
    protected $index = 'cmf_gift_record';

    /**
     * 魅力榜
     * @param $start_time
     * @param $end_time
     * @param $page
     * @return mixed
     */
    public function glamourList($start_time, $end_time, $page){
        $body = '{
                  "query": {
                    "range": {
                      "addtime": {
                        "gte": _start,
                        "lte": _end
                      }
                    }
                  },
                  "aggs": {
                    "group": {
                      "terms": {
                        "field": "touid",
                        "size": 1000000, 
                        "order": {
                          "sum_total": "desc"
                        }
                      },
                      "aggs": {
                        "sum_total": {
                          "sum": {
                            "field": "totalcoin"
                          }
                        },
                        "bucket_field":{
                          "bucket_sort":{
                              "from":_page,
                              "size":_offTotal
                          }
                        }
                      }
                    }
                  }
                }';
        list($page, $page_total) = $this->turn_page($page, 10);
        $data = [
            '_start'    => $start_time,
            '_end'      => $end_time,
            '_page'     => $page,
            '_offTotal' => $page_total,
        ];
        $body = $this->turn($body, $data);
        $res  = $this->_search($body);
        return $res['aggregations']['group']['buckets'];
    }

    /**
     * 获取贡献前三
     * @param $start
     * @param $end
     * @param $uid
     * @return mixed
     */
    public function getThree($start, $end, $uid){
        $body = '{
                  "size": 0, 
                  "query": {
                    "bool": {
                      "must": [
                        {
                          "match": {
                            "touid": "_toUid"
                          }
                        }
                      ],
                      "filter": {
                        "range": {
                          "addtime": {
                            "gte": _start,
                            "lte": _end
                          }
                        }
                      }
                    }
                  },
                  "aggs": {
                    "group": {
                      "terms": {
                        "field": "uid",
                        "size": 1000000,
                        "order": {
                          "sums": "desc"
                        }
                      },
                      "aggs": {
                        "sums": {
                          "sum": {
                            "field": "totalcoin"
                          }
                        },
                        "bucket_field": {
                          "bucket_sort": {
                            "from": 0,
                            "size": 3
                          }
                        }
                      }
                    }
                  }
                }';

        $data = [
            '_start' => $start,
            '_end'   => $end,
            '_toUid' => $uid,
        ];
        $body = $this->turn($body, $data);
        $res  = $this->_search($body);
        return $res['aggregations']['group']['buckets'];
    }

    public function wealthList($start,$end,$page)
    {
        $body = '{
                  "query": {
                    "range": {
                      "addtime": {
                        "gte": _start,
                        "lte": _end
                      }
                    }
                  },
                  "aggs": {
                    "group": {
                      "terms": {
                        "field": "uid",
                        "size": 1000000, 
                        "order": {
                          "sum_total": "desc"
                        }
                      },
                      "aggs": {
                        "sum_total": {
                          "sum": {
                            "field": "totalcoin"
                          }
                        },
                        "bucket_field":{
                          "bucket_sort":{
                              "from":_page,
                              "size":_offTotal
                          }
                        }
                      }
                    }
                  }
                }';
        list($page, $page_total) = $this->turn_page($page, 10);
        $data = [
            '_start'    => $start,
            '_end'      => $end,
            '_page'     => $page,
            '_offTotal' => $page_total,
        ];
        $body = $this->turn($body, $data);
        $res  = $this->_search($body);
        return $res['aggregations']['group']['buckets'];
    }

    /**
     * 测试
     * @param $start_time
     * @param $end_time
     * @param $page
     * @return mixed
     */
    public function glamourListTest($start_time, $end_time){
        $body = '{
                  "query": {
                    "range": {
                      "addtime": {
                        "gte": _start,
                        "lte": _end
                      }
                    }
                  },
                  "aggs": {
                    "group": {
                      "terms": {
                        "field": "touid",
                        "size": 1000000, 
                        "order": {
                          "sum_total": "desc"
                        }
                      },
                      "aggs": {
                        "sum_total": {
                          "sum": {
                            "field": "totalcoin"
                          }
                        },
                        "bucket_field":{
                          "bucket_sort":{
                              "size":2000
                          }
                        }
                      }
                    }
                  }
                }';
        $data = [
            '_start'    => $start_time,
            '_end'      => $end_time,
        ];
        $body = $this->turn($body, $data);
        $res  = $this->_search($body);
        return $res['aggregations']['group']['buckets'];
    }

    /**
     * 魅力榜
     * @param $start_time
     * @param $end_time
     * @param $page
     * @return mixed
     */
    public function goalList($start_time, $end_time){
        $body = '{
                  "query": {
                    "match_all": {
                        "range": {
                          "addtime": {
                            "gte": _start,
                            "lte": _end
                          }
                    }
                    }
                  }
                }';
        $data = [
            '_start'    => $start_time,
            '_end'      => $end_time,
        ];
        $body = $this->turn($body, $data);
        $res  = $this->_search($body);
        return array_column($res['hits']['hits'], '_source');
    }
}