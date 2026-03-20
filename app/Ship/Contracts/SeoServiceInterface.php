<?php

namespace App\Ship\Contracts;


use Illuminate\Database\Eloquent\Model;

interface SeoServiceInterface
{
    public function getSeoForModel(Model $model): ?array;

    public function getSeoForCurrentPage(): array|null;
}