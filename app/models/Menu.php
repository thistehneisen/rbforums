<?php

class Menu {
    public static function top()
    {
        $data['menu'] = [];
        if(Auth::check()) {
            $data['menu'] = [
                'day1' => 'admin.day-1',
                'day2' => 'admin.day-2',
                'media' => 'admin.media',
                'contacts' => 'admin.contacts',
	            'export' => 'admin.export',
            ];
        }
        return View::make('admin.top_menu', $data);
    }
}