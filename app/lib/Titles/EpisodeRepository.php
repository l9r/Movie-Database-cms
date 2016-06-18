<?php namespace Lib\Titles;

use Episode, Event;
use Lib\Services\Db\Writer;

class EpisodeRepository
{
    /**
     * dbWriter instance.
     * 
     * @var \Lib\Services\Db\Writer
     */
    private $dbWriter;

    /**
     * Episode model instance.
     * 
     * @var Episode
     */
    private $episode;


    public function __construct(Writer $dbWriter, Episode $episode)
    {
        $this->episode = $episode;
        $this->dbWriter = $dbWriter;
    }

    /**
     * Create new episode in database.
     * 
     * @param  array $input
     * @return void
     */
    public function create(array $input)
    {
        $this->dbWriter->CompileInsert('episodes', $input)->save();

        Event::fire('Titles.Modified', array($input['title_id']));
    }

    /**
     * Update existing episode in database.
     *
     * @param int/string $id
     * @param  array $input
     * @return void
     */
    public function update($id, array $input)
    {
        $this->episode->where('id', $id)->update($input);

        Event::fire('Titles.Modified', array($input['title_id']));
    }


    /**
     * Handles episode deletion.
     * 
     * @param  string $episode
     * @return void
     */
    public function delete($episode)
    {
        $this->episode->destroy($episode);
    }
}