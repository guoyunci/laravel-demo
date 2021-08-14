<?php

namespace App\Services;

use App\Models\SearchHistory;

class SearchHistoryServices extends BaseServices
{
    /**
     * @param $userId
     * @param $keyword
     * @param $from
     * @return SearchHistory
     */
    public function save($userId, $keyword, $from): SearchHistory
    {
        $history = SearchHistory::new();
        $history->fill([
            'user_id' => $userId,
            'keyword' => $keyword,
            'from' => $from
        ]);
        $history->save();
        return $history;
    }
}
