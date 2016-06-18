<?php

class OptionsSeeder extends Seeder {

    public function run()
    {
        DB::table('options')->insert(array(
            array('name' => 'installed', 'value' => 1),
            array('name' => 'data_provider', 'value' => 'imdb'),
            array('name' => 'search_provider', 'value' => 'imdb'),
            array('name' => 'updated', 'value' => 1),
            array('name' => 'genres', 'value' => 'Action|Adventure|Animation|Biography|Comedy|Crime|Documentary|Drama|Family|Fantasy|History|Horror|Music|Musical|Mystery|Romance|Sci-Fi|Thriller|War|Western'),
            array('name' => 'menus', 'value' => '[{"name":"TopMenu","position":"header","active":"1","items":[{"label":"Movies","action":"movies.index","weight":"1","type":"route","children":[],"visibility":"everyone"},{"label":"Series","action":"series.index","weight":"2","type":"route","children":[],"visibility":"everyone"},{"label":"News","action":"news.index","weight":"3","type":"route","children":[],"visibility":"everyone"},{"label":"People","action":"people.index","weight":"4","type":"route","children":[],"visibility":"everyone"},{"label":"Dashboard","action":"dashboard","weight":"5","type":"route","children":[],"visibility":"admin"}]},{"name":"FooterMenu","position":"footer","active":"1","items":[{"label":"Privacy Policy","action":"privacy-policy","weight":1,"type":"page","children":[],"visibility":"everyone"},{"label":"Terms of Service","action":"tos","weight":"2","type":"page","children":[],"visibility":"everyone"}]}]'),
        ));
    }

}