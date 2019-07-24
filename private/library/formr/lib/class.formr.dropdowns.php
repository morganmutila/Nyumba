<?php

class Dropdowns extends MyDropdowns
{

    // contains arrays of US states, Canadian provinces/territories, countries of the world and many more - to be used in dropdown menus
    // you can easily add your own and then call them by using the input_select() function or in the fastform() function

    // months with full name as key
    public static function months()
    {
        return [
            'January' => 'January',
            'February' => 'February',
            'March' => 'March',
            'April' => 'April',
            'May' => 'May',
            'June' => 'June',
            'July' => 'July',
            'August' => 'August',
            'September' => 'September',
            'October' => 'October',
            'November' => 'November',
            'December' => 'December'
        ];
    }

    public static function days()
    {
        $stop_day = 31;

        // get the current year
        $start_day = 1;

        // initialize the years array
        $days = [];

        // starting with the current year, 
        // loop through the years until we reach the stop date
        for ($i = $start_day; $i <= $stop_day; $i++) {
            $days[$i] = $i;
        }

        return $days;
    }

    # displays every year starting from 1950, good for registration forms
    public static function years()
    {
        $stop_date = date('Y');

        // get the current year
        $start_date = 1950;

        // initialize the years array
        $years = [];

        // starting with the current year, 
        // loop through the years until we reach the stop date
        for ($i = $start_date; $i <= $stop_date; $i++) {
            $years[$i] = $i;
        }

        // reverse the array so we have 1900 at the bottom of the menu
        $return = array_reverse($years, true);

        return $return;
    }

    // displays months of the year
    public static function months_alpha()
    {
        return [
            1  => 'January',
            2  => 'February',
            3  => 'March',
            4  => 'April',
            5  => 'May',
            6  => 'June',
            7  => 'July',
            8  => 'August',
            9  => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
    }

    // months - good for credit cards
    public static function cc_months()
    {
        return [
            1  => '01 - January',
            2  => '02 - February',
            3  => '03 - March',
            4  => '04 - April',
            5  => '05 - May',
            6  => '06 - June',
            7  => '07 - July',
            8  => '08 - August',
            9  => '09 - September',
            10 => '10 - October',
            11 => '11 - November',
            12 => '12 - December'
        ];
    }


    // years - for credit cards
    public static function cc_years()
    {
        $stop_date = 2025;

        // get the current year
        $current_year = date('Y');

        // initialize the years array
        $years = [];

        // starting with the current year, 
        // loop through the years until we reach the stop date
        for ($i = $current_year; $i <= $stop_date; $i++) {
            $years[$i] = $i;
        }

        return $years;
    }


    public static function height()
    {
        return [
            '3-0' => "Under 4'",
            '4-0' => "4' 0&quot",
            '4-1' => "4' 1&quot",
            '4-2' => "4' 2&quot",
            '4-3' => "4' 3&quot",
            '4-4' => "4' 4&quot",
            '4-5' => "4' 5&quot",
            '4-6' => "4' 6&quot",
            '4-7' => "4' 7&quot",
            '4-8' => "4' 8&quot",
            '4-9' => "4' 9&quot",
            '4-10' => "4' 10&quot",
            '4-11' => "4' 11&quot",
            '5-0' => "5' 0&quot",
            '5-1' => "5' 1&quot",
            '5-2' => "5' 2&quot",
            '5-3' => "5' 3&quot",
            '5-4' => "5' 4&quot",
            '5-5' => "5' 5&quot",
            '5-6' => "5' 6&quot",
            '5-7' => "5' 7&quot",
            '5-8' => "5' 8&quot",
            '5-9' => "5' 9&quot",
            '5-10' => "5' 10&quot",
            '5-11' => "5' 11&quot",
            '6-0' => "6' &amp; Over",
        ];
    }


    // makes sure a person is of a certain age - in this cas: 18
    public static function years_old()
    {
        $stop_date = date('Y', strtotime('-18 year'));

        $start_date = 1950;

        // initialize the years array
        $years = [];

        // starting with the current year, 
        // loop through the years until we reach the stop date
        for ($i = $start_date; $i <= $stop_date; $i++) {
            $years[$i] = $i;
        }

        // reverse the array so we have the start date at the bottom of the menu
        $return = array_reverse($years, true);

        return $return;
    }


    public static function age()
    {
        foreach (range(18, 24) as $value) {
            $ages[$value] = $value;
        }
        $ages[25] = '25-29';
        $ages[30] = '30-34';
        $ages[35] = '35-39';
        $ages[40] = '40-44';
        $ages[45] = '45-49';
        $ages[50] = '50+';

        return $ages;
    }


   
}
