<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{

    public function courseList()
    {
        // returning all the course list

        try {
            $result = Course::select('name', 'description', 'thumbnail', 'lesson_num', 'price', 'id')->get();


            return response()->json([
                'code' => 200,
                'msg' => 'My course List ',
                'data' => $result
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'msg' => $th->getMessage(),

            ], 200);
        }


    }

    public function courseDetail(Request $request)
    {
        // returning all the course list
        $id = $request->id;
        try {
            $result = Course::where('id', '=', $id)->select(
                'id',

                'name',
                'user_token',
                'description',
                'video_length',
                'thumbnail',
                'lesson_num',
                'price',
                'downloadable_resources'
            )->first();
            // get retun List
            // first() return map
            // $result = Course::where('id', '=', $id)->first();


            // print($result);
            return response()->json([
                'code' => 200,
                'msg' => 'My course details are here ',
                'data' => $result
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'msg' => $th->getMessage(),

            ], 200);
        }


    }
}