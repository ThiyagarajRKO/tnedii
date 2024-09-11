<?php

namespace Impiger\SimpleSlider\Models;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Base\Traits\EnumCastable;
use Impiger\Base\Models\BaseModel;

class SimpleSlider extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'simple_sliders';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'key',
        'description',
        'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sliderItems()
    {
        return $this->hasMany(SimpleSliderItem::class)->orderBy('simple_slider_items.order', 'asc');
    }

    protected static function boot()
    {
        parent::boot();

        self::deleting(function (SimpleSlider $slider) {
            SimpleSliderItem::where('simple_slider_id', $slider->id)->delete();
        });
    }
}
