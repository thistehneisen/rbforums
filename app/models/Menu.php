<?php

class Menu {
    public static function top()
    {
        $data['menu'] = [];
        if(Auth::check()) {
            $data['menu'] = [
//                'votes' => 'admin.votes',
	            'bans' => 'admin.bans',
	            'bans2' => 'admin.bans_150',
//	            'export' => 'admin.export',
            ];
        }
        return View::make('admin.top_menu', $data);
    }
}