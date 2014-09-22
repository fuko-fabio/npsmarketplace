<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class EventsCollector {

    public static function getEvents() {
        return EventsCollector::mocked();
    }

    public static function mocked() {
        return array(
            'title' => 'Sprawdź kalendarz',
            'no_events' => 'Brak wydarzeń',
            'month' => 'Wrzesień',
            'year' => 2014,
            'days' => array(
                array(
                    'day' => 1,
                    'name' => 'Poniedziałek',
                    'events' => array(
                        array(
                            'name' => 'Joga',
                            'time' => '18:00'
                        ),
                        array(
                            'name' => 'Workshop',
                            'time' => '19:00'
                        )
                    )
                ),
                array(
                    'day' => 2,
                    'name' => 'Wtorek',
                    'events' => array(
                        array(
                            'name' => 'Workshop',
                            'time' => '15:00'
                        )
                    )
                ),
                array(
                    'day' => 3,
                    'name' => 'Środa',
                    'events' => array(
                        array(
                            'name' => 'Pool Dance',
                            'time' => '20:00'
                        ),
                        array(
                            'name' => 'Gym',
                            'time' => '21:00'
                        ),
                        array(
                            'name' => 'Night run',
                            'time' => '23:00'
                        )
                    )
                ),
                array(
                    'day' => 4,
                    'name' => 'Czwartek',
                    'events' => array()
                ),
                array(
                    'day' => 5,
                    'name' => 'Piątek',
                    'events' => array(
                        array(
                            'name' => 'Workshop',
                            'time' => '12:00'
                        ),
                        array(
                            'name' => 'Gym',
                            'time' => '16:00'
                        ),
                        array(
                            'name' => 'Pool Dance',
                            'time' => '23:00'
                        )
                    )
                ),
                array(
                    'day' => 6,
                    'name' => 'Sobota',
                    'events' => array(
                        array(
                            'name' => 'Workshop',
                            'time' => '11:00'
                        )
                    )
                ),
                array(
                    'day' => 7,
                    'name' => 'Niedziela',
                    'events' => array(
                        array(
                            'name' => 'Workshop',
                            'time' => '12:00'
                        ),
                        array(
                            'name' => 'Joga',
                            'time' => '16:00'
                        ),
                        array(
                            'name' => 'Gym',
                            'time' => '19:00'
                        ),
                        array(
                            'name' => 'Pool Dance',
                            'time' => '23:00'
                        ),
                        array(
                            'name' => 'Night run',
                            'time' => '23:00'
                        )
                    )
                )
            )
        );
    }
}
