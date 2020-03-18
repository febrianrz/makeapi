<?php

namespace Febrianrz\Makeapi;

use Webpatser\Uuid\Uuid;

trait HasUuid
{
    /**
     * Add id to casts Model property.
     *
     * @var array
     */
    protected function getArrayableItems(array $values)
    {
        if (!isset($this->casts['id'])) {
            $this->casts['id'] = 'string';
        }
        return parent::getArrayableItems($values);
    }

    /**
     * Boot function from laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::generate()->string;
        });
    }
}
