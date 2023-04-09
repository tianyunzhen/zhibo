<?php

class Elast_JackpotRecord extends Elast_Base{
    protected $index = 'cmf_jackpot_record';

    public function countList($startTime, $endTime, $giftId, $page){
        $body = '{
                  "size": 0, 
                  "query": {
                    "bool": {
                      "must": [
                        {
                          "match": {
                            "gift_id": "_giftId"
                          }
                        }
                      ],
                      "filter": {
                        "range": {
                          "create_time": {
                            "gte": _startTime,
                            "lte": _endTime
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
                          "count_multiple": "desc"
                        }
                      },
                      "aggs": {
                        "count_multiple": {
                          "sum": {
                            "field": "multiple"
                          }
                        },
                        "bucket_field": {
                          "bucket_sort": {
                            "from": _limit,
                            "size": _pageTotal
                          }
                        }
                      }
                    }
                  }
                }';

        list($page, $page_total) = $this->turn_page($page, 10);
        $data = [
            '_startTime' => $startTime,
            '_endTime'   => $endTime,
            '_limit'     => $page,
            '_pageTotal' => $page_total,
            '_giftId'    => $giftId,
        ];
        $body = $this->turn($body, $data);
        $res  = $this->_search($body);
        return $res['aggregations']['group']['buckets'];
    }

    public function getUserJackpotInfo($uid, $startTime, $endTime, $giftId){
        $body = '{
                  "size": 0, 
                  "query": {
                    "bool": {
                      "must": [
                        {"term": {
                          "gift_id": {
                            "value": "_giftId"
                          }
                        }},
                        {"term": {
                          "uid": {
                            "value": "_uid"
                          }
                        }}
                      ],
                      "filter": {
                        "range": {
                          "create_time": {
                            "gte": _startTime,
                            "lte": _endTime
                          }
                        }
                      }
                    }
                  },
                  "aggs": {
                    "count_multiple": {
                      "sum": {
                        "field": "multiple"
                      }
                    }
                  }
                }';
        $data = [
            '_startTime' => $startTime,
            '_endTime'   => $endTime,
            '_uid'       => $uid,
            '_giftId'    => $giftId,
        ];
        $body = $this->turn($body, $data);
        $res  = $this->_search($body);
        return $res['aggregations']['count_multiple']['values'] ?? 0;
    }
    
}