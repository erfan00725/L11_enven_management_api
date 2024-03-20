<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Nette\Utils\Arrays;

trait CanLoadRelations
{

    protected function shouldIncloudeRelation(string $relation): bool{
        $include = request()->query('include');

        if (!$include) {
            return false;
        }

        $relations = array_map('trim', explode(',', $include));

        return in_array($relation, $relations);
    }

    public function loadRelations(
        Model|QueryBuilder|EloquentBuilder $for,
        ?array $relations = null
    )
    {
        $relations = $relations ?? $this->relations ?? [];

        foreach ($relations as $relation) {
            $for->when($this->shouldIncloudeRelation($relation),
                fn($query) => $for instanceof Model ? $for->load($relation) : $for->with($relation)
            );
        }

        return $for;
    }
}
