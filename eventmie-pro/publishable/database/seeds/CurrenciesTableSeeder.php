<?php

use Illuminate\Database\Seeder;
use Classiebit\Eventmie\Models\Currency;

class CurrenciesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $currency = Currency::count();
        if($currency) 
            return true;

        $currency = $this->currency('id', 1);
        if (!$currency->exists) {
            \DB::table('currencies')->insert(array (
                0 => 
                array (
                    'id' => 1,
                    'iso_code' => 'AED',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                1 => 
                array (
                    'id' => 2,
                    'iso_code' => 'ANG',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                2 => 
                array (
                    'id' => 3,
                    'iso_code' => 'AOA',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                3 => 
                array (
                    'id' => 4,
                    'iso_code' => 'ARS',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                4 => 
                array (
                    'id' => 5,
                    'iso_code' => 'AUD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                5 => 
                array (
                    'id' => 6,
                    'iso_code' => 'BAM',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                6 => 
                array (
                    'id' => 7,
                    'iso_code' => 'BBD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                7 => 
                array (
                    'id' => 8,
                    'iso_code' => 'BGL',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                8 => 
                array (
                    'id' => 9,
                    'iso_code' => 'BHD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                9 => 
                array (
                    'id' => 10,
                    'iso_code' => 'BND',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                10 => 
                array (
                    'id' => 11,
                    'iso_code' => 'BRL',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                11 => 
                array (
                    'id' => 12,
                    'iso_code' => 'CAD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                12 => 
                array (
                    'id' => 13,
                    'iso_code' => 'CHF',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                13 => 
                array (
                    'id' => 14,
                    'iso_code' => 'CLF',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                14 => 
                array (
                    'id' => 15,
                    'iso_code' => 'CLP',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                15 => 
                array (
                    'id' => 16,
                    'iso_code' => 'CNY',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                16 => 
                array (
                    'id' => 17,
                    'iso_code' => 'COP',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                17 => 
                array (
                    'id' => 18,
                    'iso_code' => 'CRC',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                18 => 
                array (
                    'id' => 19,
                    'iso_code' => 'CZK',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                19 => 
                array (
                    'id' => 20,
                    'iso_code' => 'DKK',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                20 => 
                array (
                    'id' => 21,
                    'iso_code' => 'EEK',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                21 => 
                array (
                    'id' => 22,
                    'iso_code' => 'EGP',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                22 => 
                array (
                    'id' => 23,
                    'iso_code' => 'EUR',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                23 => 
                array (
                    'id' => 24,
                    'iso_code' => 'FJD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                24 => 
                array (
                    'id' => 25,
                    'iso_code' => 'GBP',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                25 => 
                array (
                    'id' => 26,
                    'iso_code' => 'GTQ',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                26 => 
                array (
                    'id' => 27,
                    'iso_code' => 'HKD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                27 => 
                array (
                    'id' => 28,
                    'iso_code' => 'HRK',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                28 => 
                array (
                    'id' => 29,
                    'iso_code' => 'HUF',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                29 => 
                array (
                    'id' => 30,
                    'iso_code' => 'IDR',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                30 => 
                array (
                    'id' => 31,
                    'iso_code' => 'ILS',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                31 => 
                array (
                    'id' => 32,
                    'iso_code' => 'INR',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                32 => 
                array (
                    'id' => 33,
                    'iso_code' => 'JOD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                33 => 
                array (
                    'id' => 34,
                    'iso_code' => 'JPY',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                34 => 
                array (
                    'id' => 35,
                    'iso_code' => 'KES',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                35 => 
                array (
                    'id' => 36,
                    'iso_code' => 'KRW',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                36 => 
                array (
                    'id' => 37,
                    'iso_code' => 'KWD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                37 => 
                array (
                    'id' => 38,
                    'iso_code' => 'KYD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                38 => 
                array (
                    'id' => 39,
                    'iso_code' => 'LTL',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                39 => 
                array (
                    'id' => 40,
                    'iso_code' => 'LVL',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                40 => 
                array (
                    'id' => 41,
                    'iso_code' => 'MAD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                41 => 
                array (
                    'id' => 42,
                    'iso_code' => 'MVR',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                42 => 
                array (
                    'id' => 43,
                    'iso_code' => 'MXN',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                43 => 
                array (
                    'id' => 44,
                    'iso_code' => 'MYR',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                44 => 
                array (
                    'id' => 45,
                    'iso_code' => 'NGN',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                45 => 
                array (
                    'id' => 46,
                    'iso_code' => 'NOK',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                46 => 
                array (
                    'id' => 47,
                    'iso_code' => 'NZD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                47 => 
                array (
                    'id' => 48,
                    'iso_code' => 'OMR',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                48 => 
                array (
                    'id' => 49,
                    'iso_code' => 'PEN',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                49 => 
                array (
                    'id' => 50,
                    'iso_code' => 'PHP',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                50 => 
                array (
                    'id' => 51,
                    'iso_code' => 'PLN',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                51 => 
                array (
                    'id' => 52,
                    'iso_code' => 'QAR',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                52 => 
                array (
                    'id' => 53,
                    'iso_code' => 'RON',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                53 => 
                array (
                    'id' => 54,
                    'iso_code' => 'RUB',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                54 => 
                array (
                    'id' => 55,
                    'iso_code' => 'SAR',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                55 => 
                array (
                    'id' => 56,
                    'iso_code' => 'SEK',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                56 => 
                array (
                    'id' => 57,
                    'iso_code' => 'SGD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                57 => 
                array (
                    'id' => 58,
                    'iso_code' => 'THB',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                58 => 
                array (
                    'id' => 59,
                    'iso_code' => 'TRY',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                59 => 
                array (
                    'id' => 60,
                    'iso_code' => 'TTD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                60 => 
                array (
                    'id' => 61,
                    'iso_code' => 'TWD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                61 => 
                array (
                    'id' => 62,
                    'iso_code' => 'UAH',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                62 => 
                array (
                    'id' => 63,
                    'iso_code' => 'USD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                63 => 
                array (
                    'id' => 64,
                    'iso_code' => 'VEF',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                64 => 
                array (
                    'id' => 65,
                    'iso_code' => 'VND',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                65 => 
                array (
                    'id' => 66,
                    'iso_code' => 'XCD',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
                66 => 
                array (
                    'id' => 67,
                    'iso_code' => 'ZAR',
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
            ));
        }    
        
        
    }

    protected function currency($field, $for)
    {
        return Currency::firstOrNew([$field => $for]);
    }
}