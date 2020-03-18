<?php

namespace Febrianrz\Makeapi;

use DB;
use Exception;

/**
 * Simple Select2 response builder like DataTables
 */
class Select2
{
    public static function empty()
    {
        return ['results' => []];
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param array                              $template
     */
    public static function of($query, $idColumn, $textColumn, $additionalColumn = [], $filterCallback = null)
    {
        $textColumn = (string) $textColumn;
        $mainColumns = [
            DB::raw("{$idColumn} as id"),
            DB::raw("{$textColumn} as text"),
        ];
        $columns = array_merge($mainColumns, $additionalColumn);

        if (request()->has('term')) {
            if (is_null($filterCallback)) {
                $term = request()->input('term');
                $query->whereRaw("{$textColumn} like ?", ["%{$term}%"]);
            } else {
                $filterCallback($query, request()->input('term'));
            }
        }

        return ['results' => $query->select($columns)->get()];
    }
}
