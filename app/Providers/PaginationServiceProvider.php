<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class PaginationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        EloquentBuilder::macro('paginateAuto', function (
            $perPage = null,
            ...$args
        ) {
            $perPage = PaginationServiceProvider::getPerPage(
                $perPage,
                $this->model->getPerPage(),
            );

            return $this->paginate($perPage, ...$args);
        });

        QueryBuilder::macro('paginateAuto', function (
            $perPage = null,
            ...$args
        ) {
            $perPage = PaginationServiceProvider::getPerPage($perPage);

            return $this->paginate($perPage, ...$args);
        });
    }

    public static function getPerPage($perPage = null, $defaultPerPage = null)
    {
        $defaultPerPage = $defaultPerPage ?? 15;

        if ($perPage === null) {
            $perPage = request()->query('per_page');

            $maxPerPage = 1000;

            $perPage =
                filter_var($perPage, FILTER_VALIDATE_INT) === false ||
                (int) $perPage < 1
                    ? $defaultPerPage
                    : min((int) $perPage, $maxPerPage);
        }

        return $perPage;
    }
}
