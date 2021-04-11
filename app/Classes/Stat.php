<?php

namespace App\Classes;

class Stat
{
    public function statistics()
    {
        $postsData = (new Post())->getPosts();

        $response = [
            'avg_character_length_month'          => $this->avgCharacterLengthPerMonth($postsData),
            'longest_character_length_post_month' => $this->longestPostPerMonth($postsData),
            'avg_post_user_per_month'             => $this->avgUserPostMonthly($postsData),
        ];

        echo json_encode($response);
    }

    public function avgCharacterLengthPerMonth($data)
    {
        $sortedData = $this->sortArrByMonth($data);

        $avgCharacterLengthPerMonthArr = [];
        foreach ($sortedData as $key => $arr) {
            $arrSrtLen                           = array_column($arr, 'str_len');
            $avgCharacterLengthPerMonthArr[$key] = number_format(array_sum($arrSrtLen) / count($arrSrtLen), 2);
        }

        return $avgCharacterLengthPerMonthArr;
    }

    public function longestPostPerMonth($data)
    {
        $sortedData = $this->sortArrByMonth($data);

        $longestPostPerMonth = [];
        foreach ($sortedData as $key => $arr) {
            $arrSrtLen = array_column($arr, 'str_len');
            array_multisort($arrSrtLen, SORT_ASC, $arr);
            $longestPostPerMonth[$key] = end($arr);
        }

        return $longestPostPerMonth;
    }

    public function avgUserPostMonthly($data)
    {
        $sortedData   = [];
        $uniqueMonths = [];
        foreach ($data as $element) {
            $user = $element['from_id'];
            $date = date("Y-M", strtotime($element['created_time']));

            if (!in_array($date, $uniqueMonths)) {
                array_push($uniqueMonths, $date);
            }

            if (!isset($sortedData[$user])) {
                $sortedData[$user] = [$element];
            } else {
                $sortedData[$user][] = $element;
            }
        }

        $avgUserPostPerMonth = [];
        foreach ($sortedData as $key => $arr) {
            $avgUserPostPerMonth[$key] = number_format(count($arr) / count($uniqueMonths), 2);
        }

        return $avgUserPostPerMonth;
    }

    public function sortArrByMonth($array)
    {
        $result = [];
        foreach ($array as $element) {
            $date               = date("Y-M", strtotime($element['created_time']));
            $strLen             = strlen($element['message']) - substr_count($element['message'], ' ');
            $element['str_len'] = $strLen;
            if (!isset($result[$date])) {
                $result[$date] = [$element];
            } else {
                $result[$date][] = $element;
            }
        }

        return $result;
    }
}