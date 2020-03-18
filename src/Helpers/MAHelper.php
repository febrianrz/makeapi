<?php
namespace Febrianrz\Makeapi\Helpers;

use Illuminate\Http\Request;

class MAHelper {
    public static function filter(Request $request, &$query){
        $order_type = $request->has('order_type') ? $request->order_type : 'asc';
        if($request->has('order_by')){
            $arr = explode(',',$request->order_by);
            foreach($arr as $val){
                if(self::isHasColumn($query,$val))
                    $query->orderBy($val,$order_type); 
                else 
                    return abort(400,"Invalid order by {$val}");
            }
        }

        if($request->has('where')){
            $where = $request->where;
            if(is_array($where)){
                foreach($where as $key => $val){
                    if(self::isHasColumn($query, $key)){
                        $query->where($key,'like',"%{$val}%");
                    } else {
                        return abort(400,"Invalid where {$key}");
                    }
                }
            }
        }
    }

    public static function isHasColumn($query, $column_name){
        return $query->getConnection()
            ->getSchemaBuilder()->hasColumn($query->getModel()->getTable(),$column_name);
    }
}