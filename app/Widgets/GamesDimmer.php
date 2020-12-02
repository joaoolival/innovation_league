<?php

namespace App\Widgets;

use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Widgets\BaseDimmer;

class GamesDimmer extends BaseDimmer
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count = Game::count();
        $string = trans_choice('Games', $count);

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-controller',
            'title'  => "{$count} {$string}",
            'text'   => "",
            'button' => [
                'text' => 'Games',
                'link' => route('voyager.games.index'),
            ],
            'image' => asset('assets/widgets/games_background.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return Auth::user()->can('browse', app(Game::class));

    }
}
