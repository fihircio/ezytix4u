<?php

use Illuminate\Database\Seeder;
use Classiebit\Eventmie\Models\Event;

class EventsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $event = Event::count();
        if($event) 
            return true;

        $event = $this->event('id', 1);
        if (!$event->exists) {
            $event->fill([
                'title' => 'Digital Marketing Seminar',
                'description' => 'Resolution diminution conviction so mr at unpleasing simplicity',
                'excerpt' => 'Resolution diminution conviction so mr at unpleasing simplicity',
                'thumbnail' => 'events/November2023/1701172866bLnmsXyA3e.webp',
                'poster' => 'events/November2023/1701172867AxxY4MHTU5.webp',
                'images' => '["events\\/November2023\\/1701172867248.webp","events\\/November2023\\/1701172867248.webp","events\\/November2023\\/1701172867248.webp","events\\/November2023\\/1701172867248.webp","events\\/November2023\\/1701172867248.webp","events\\/November2023\\/1701172867248.webp"]',
                'video_link' => 'y2Ky3Wo37AY',
                'venue' => 'History Museum',
                'address' => 'Feet evil, occasional boisterous',
                'city' => 'Nagano',
                'state' => 'Chūbu',
                'zipcode' => '111-1212',
                'country_id' => 110,
                'start_date' => '2025-11-29',
                'end_date' => '2025-11-29',
                'start_time' => '08:00:00',
                'end_time' => '10:30:00',
                'repetitive' => 0,
                'featured' => 0,
                'status' => 1,
                'meta_title' => NULL,
                'meta_keywords' => NULL,
                'meta_description' => NULL,
                'category_id' => 1,
                'user_id' => 2,
                'add_to_facebook' => NULL,
                'slug' => 'digital-marketing-seminar',
                'price_type' => 1,
                'latitude' => '36.648138',
                'longitude' => '137.9744245',
                'item_sku' => "1567428779193",
                'publish' => 1,
                'is_publishable' => '{"detail":1,"location":1,"timing":1,"publish":1,"media":1,"tickets":1}',
                'merge_schedule' => 0,
            ])->save();
        }
        
    }

    protected function event($field, $for)
    {
        return Event::firstOrNew([$field => $for]);
    }
}