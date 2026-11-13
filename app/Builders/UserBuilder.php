<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * A custom query builder for the User model, providing methods to filter and sort user records based on various criteria.
 * 
 * @method UserBuilder filter(array $filters)
 * @method UserBuilder sortByField(?string $field, ?string $direction)
 */
class UserBuilder extends Builder
{
    /**
     * Applies filters to the query based on the provided array of filter criteria.
     * @param array $filters
     * @return UserBuilder|\Illuminate\Database\Query\Builder
     */
    public function filter(array $filters): self
    {
        return $this
            ->when(!empty($filters['search']), function (self $query) use ($filters) {
                $search = $filters['search'];
                $query->where(function (self $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when(!empty($filters['role']), fn (self $query) => $query->role($filters['role']))
            ->when(!empty($filters['status']), fn (self $query) => $query->where('status', $filters['status']));
    }

    /**
     * Sorts the query results based on the specified field and direction. If no field or direction is provided, it defaults to sorting by 'created_at' in descending order.
     * @param ?string $field
     * @param ?string $direction
     * @return UserBuilder
     */
    public function sortByField(?string $field, ?string $direction): self
    {
        return $this->orderBy(
            $field ?? 'created_at',
            $direction ?? 'desc'
        );
    }
}