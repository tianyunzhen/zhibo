<?php

class Elast_FamilyProfit extends Elast_Base{
    protected $index = 'cmf_family_profit';

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
                        "field": "familyid",
                        "size": 1000000, 
                        "order": {
                          "sum_total": "desc"
                        }
                      },
                      "aggs": {
                        "sum_total": {
                          "sum": {
                            "field": "total"
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
}