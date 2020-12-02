<?php

namespace App\Widgets;

use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Widgets\BaseDimmer;

class GroupsDimmer extends BaseDimmer
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
        $count = Group::count();
        $string = trans_choice('Groups', $count);

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-pie-graph',
            'title'  => "{$count} {$string}",
            'text'   => "",
            'button' => [
                'text' => 'Groups',
                'link' => route('voyager.groups.index'),
            ],
            'image' => asset('assets/widgets/groups_background.png'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return Auth::user()->can('browse', app(Group::class));

    }
}
