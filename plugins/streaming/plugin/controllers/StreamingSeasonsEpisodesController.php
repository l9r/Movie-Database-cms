<?php

class StreamingSeasonsEpisodesController extends SeasonsEpisodesController
{
	public function __construct()
	{
        $repo = App::make('Lib\Titles\EpisodeRepository');
        $validator = App::make('Lib\Services\Validation\EpisodeValidator');
        parent::__construct($validator, $repo);
	}

    public function show($series, $seasonNum, $episodeNum)
    {
        $title = Title::byId($series);

        foreach ($title->season as $value) {
            if ($value->number == $seasonNum) {
                $season = $value;
            }
        }

        if (isset($season)) {
            foreach ($season->episode as $value) {
                if ($value->episode_number == $episodeNum) {
                    $episode = $value;
                }
            }
        }
        
        if ( ! isset($episode)) {
            App::abort(404);
        }

        $links = Link::where('title_id', $title->id)->where('season', $season->number)->where('episode', $episode->episode_number)->get();

        return View::make('Titles.Episodes.Show')
                ->with('title', $title)
                ->with('episode', $episode)
                ->with('season', $season)
                ->with('links', $links);
    }
}