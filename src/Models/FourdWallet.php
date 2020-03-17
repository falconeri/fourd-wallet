<?php

namespace Falconeri\FourdWallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FourdWallet extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'user_type',
        'user_id',
        'name',
        'slug',
        'description',
        'balance'
    ];

    /**
     * @param string $name
     * @return void
     */
    public function setNameAttribute(string $name)
    {
        $this->attributes['name'] = $name;

        /**
         * Must be updated only if the model does not exist
         *  or the slug is empty
         */
        if (!$this->exists && !array_key_exists('slug', $this->attributes)) {
            $this->attributes['slug'] = Str::slug($name);
        }
    }
}
