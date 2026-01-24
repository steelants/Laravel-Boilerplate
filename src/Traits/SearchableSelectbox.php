<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;

trait SearchableSelectbox
{
    protected function searchableSelectbox($search, $model, $selected = [], $select = ['id', 'name'], $searchIn = ['name'], $count = 30): Collection
    {
		if (!empty($search)) {
			return $model::select($select)
                ->where(function($q) use ($search, $searchIn) {
                    foreach($searchIn as $col){
                        $q->orWhereLike($col, '%'.$search.'%');
                    }
                })
				->limit($count)
				->get();
		} else {
			// default, render few options, but allways with selected values
            $selected = (array) $selected;
			return $model::select($select)
				->whereIn('id', $selected)
				->union(
					$model::select($select)
					->whereNotIn('id', $selected)
					->limit(30)
				)
				->orderBy('id')
				->get();
		}
    }
}
