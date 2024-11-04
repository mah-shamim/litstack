<?php

namespace Ignite\Crud\Models\Traits;

use Ignite\Crud\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

trait HasMedia
{
    use InteractsWithMedia;

    /**
     * The key to determine which conversions in config should be used.
     *
     * @var string
     */
    protected $conversionsKey = 'default';

    /**
     * Set the conversions key.
     *
     * @param  string  $key
     * @return $this
     */
    public function setConversionsKey(string $key): self
    {
        $this->conversionsKey = $key;

        return $this;
    }

    /**
     * Media relation.
     *
     * @return morphMany
     */
    public function media(): MorphMany
    {
        // Using Lit media model.
        return $this->morphMany(Media::class, 'model');
    }

    /**
     * Register media conversions for field.
     *
     * @param  SpatieMedia  $media
     * @return void
     */
    public function registerMediaConversions(SpatieMedia $media = null): void
    {
        $possibleConversions = config('lit.mediaconversions');

        if (! array_key_exists($this->conversionsKey, $possibleConversions)) {
            throw new \Exception('The conversions key does not exist in the config.');
        }

        $this->applyCrop($this->addMediaConversion('preview'), $media);

        foreach ($possibleConversions[$this->conversionsKey] as $key => $value) {
            
            $conversion = $this->addMediaConversion($key);

            $conversion = $this->applyCrop($conversion, $media);

            $conversion->width($value[0])
                ->height($value[1])
                ->sharpen($value[2]);

            if($format = config('lit.media.format') ){
                $conversion->format($format);
            }else{
                $conversion->keepOriginalImageFormat();
            }

            
        }
    }

    /**
     * Apply crop to the given conversion.
     *
     * @param  Conversion  $conversion
     * @param  Media  $media
     * @return void
     */
    protected function applyCrop(Conversion $conversion, Media $media = null)
    {
        if (! $media) {
            return  $conversion;
        }

        if (! $media->custom_properties) {
            return  $conversion;
        }

        if (! array_key_exists('crop', $media->custom_properties)) {
            return  $conversion;
        }

        $crop = $media->custom_properties['crop'];

        return $conversion->manualCrop(
            (int) $crop['width'],
            (int) $crop['height'],
            (int) $crop['x'],
            (int) $crop['y']
        );
    }
}
