<?php

if (!function_exists('get_select_box_values')) {
    /**
     * @param array $data
     * @return array
     */
    function get_select_box_values($data,array $keyValue = [])
    {
        $dataList = [];
        $key ='id';$value = 'name';
        if(!empty($keyValue)){
            $key = $keyValue[0];
            $value = $keyValue[1];
        }
        foreach($data as $val){
            $dataList[$val[$key]] = $val[$value];
        }
        return $dataList;
    }
}

