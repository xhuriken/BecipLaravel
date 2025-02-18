<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(int $id) //unsignBigInt ?
    {
        return view('project', [
            'id' => $id,
        ]);
    }

    public function generate(int $quantity, int $year) {
        $dateTime   = new Carbon();
        $now        = new Carbon();
        $dateTime->setYear($year);
        $projects = [];
        for ($i = 1; $i < $quantity + 1; $i++):
            if( $i < 10 ) $counter = "00$i";
            else if( $i > 10 and $i < 100 ) $counter = "0$i";
            else $counter = $i;

            $smallYear = $dateTime->format('y');
            $projects[] = [
                'name'          => "B$smallYear.$counter",
                "created_at"    => $now->format('Y-m-d H:i:s'),
                "updated_at"    => $now->format('Y-m-d H:i:s'),
            ];
        endfor;

        Project::insert($projects);

        return redirect()->route('home');
    }
}
