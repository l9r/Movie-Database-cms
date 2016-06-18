<?php

class SlidesSeeder extends Seeder {

    public function run()
    {
        Db::table('slides')->insert(array(
        	array('title' => 'Edge of Tomorrow', 
                  'image' => 'http://image.tmdb.org/t/p/original/9maldNxAaXzBVgid64uWH8rwFuu.jpg',
                  'genre' => 'Action | Science Fiction',
                  'director' => 'Doug Liman',
                  'stars' => 'Emily Blunt, Tom Cruise, Bill Paxton',
                  'trailer' => 'http://www.youtube.com/embed/yUmSVcttXnI',
                  'trailer_image' => 'http://image.tmdb.org/t/p/w300/l6fmu8Xg2eH3t39vrRIBIVsrR8U.jpg',
                  'body'  => 'An officer finds himself caught in a time loop in a war with an alien race. His skills increase as he faces the same brutal combat scenarios, and his union with a Special Forces warrior gets him closer and closer to defeating the enemy.'
                ),
            array('title' => 'Godzilla', 
                  'image' => 'http://image.tmdb.org/t/p/w780/5EQWRuCHkn7qGZytyBjv2lc9I4V.jpg',
                  'genre' => 'Action | Science Fiction',
                  'director' => 'Gareth Edwards',
                  'stars' => 'Bryan Cranston, Elizabeth Olsen, Aaron Taylor-Johnson',
                  'trailer' => 'http://www.youtube.com/embed/vIu85WQTPRc',
                  'trailer_image' => 'http://image.tmdb.org/t/p/w300/eItZGFWPqkogH0u6qYPuGx9yiZU.jpg',
                  'body'  => "Fifteen years after an 'incident' at a Japanese nuclear power plant, physicist Joe Brody joins forces with his soldier son Ford to discover for themselves what really happened. What they uncover is prelude to global-threatening devastation. An epic rebirth to Toho's iconic Godzilla, this spectacular adventure pits the world's most famous monster against malevolent creatures who, bolstered by humanity's scientific arrogance, threaten our very existence."
                ),
            array('title' => 'Transformers: Age of Extinction', 
                  'image' => 'http://image.tmdb.org/t/p/w780/lhqss3NDnhy3XIPng7ylqwYniXR.jpg',
                  'genre' => 'Action | Adventure | Science Fiction',
                  'director' => 'Michael Bay',
                  'stars' => 'Nicola Peltz, Mark Wahlberg, Stanley Tucci',
                  'trailer' => 'http://www.youtube.com/embed/ubGpDoyJvmI',
                  'trailer_image' => 'http://image.tmdb.org/t/p/w300/kHqhNH7OvCXhagug4ed0ETasBWC.jpg',
                  'body'  => "Fourth part of the Transformers series, which starts a new main characters changing history."
                ),
            array('title' => 'Dawn of the Planet of the Apes', 
                  'image' => 'http://image.tmdb.org/t/p/w780/qrrkmt3erZJeRTFAKAgd3Z2b4hw.jpg',
                  'genre' => 'Action | Drama | Science Fiction | Thriller',
                  'director' => 'Matt Reeves',
                  'stars' => 'Andy Serkis, Judy Greer, Terry Notary',
                  'trailer' => 'http://www.youtube.com/embed/rf5e7Xc1Hwk',
                  'trailer_image' => 'http://image.tmdb.org/t/p/w300/rjUl3pd1LHVOVfG4IGcyA1cId5l.jpg',
                  'body'  => "A group of scientists in San Francisco struggle to stay alive in the aftermath of a plague that is wiping out humanity, while Caesar tries to maintain dominance over his community of intelligent apes."
                ),
        ));
    }

}