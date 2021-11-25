<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->has('limit') ? $request->limit : 25;

            $model = Post::applyFilters($request->only([
                    'search',
                ]))
                ->latest()
                ->paginateData($limit);
            
            $paginate = [
                'total' => (int) $model->total(),
                'currentPage' => (int) $model->currentPage(),
                'lastPage' => (int) $model->lastPage(),
                'hasMorePages' => (boolean) $model->hasMorePages(),
                'perPage' => (int) $model->perPage(),
                'total' => (int) $model->total(),
                'lastItem' => (int) $model->lastItem(),
            ];
            
            $result = api_format(true, [], $model->items(), $paginate);
            return response()->json($result);
        } catch (\Throwable $e) {
            $result = api_format(false, $e, [], []);
            return response()->json($result);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "title" => "required|max:255",
                "body" => "required",
            ]);
            
            $validator->setAttributeNames([
                'title' => 'Title',
                'body' => 'Body',
            ]);

            if ($validator->fails()) {
                $result = api_format(false, $validator->errors()->toArray(), [], []);
                return response()->json($result);
            } else {
                $create = Post::createModel($request);
                $result = api_format(true, [], [$create], []);
                return response()->json($result);
            }
        } catch (\Throwable $e) {
            $result = api_format(false, $e, [], []);
            return response()->json($result);
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        try {
            $validator = Validator::make($request->all(), [
                "title" => "required|max:255",
                "body" => "required",
            ]);
            
            $validator->setAttributeNames([
                'title' => 'Title',
                'body' => 'Body',
            ]);

            if ($validator->fails()) {
                $result = api_format(false, $validator->errors()->toArray(), [], []);
                return response()->json($result);
            } else {
                $update = $post->updateModel($request);
                $result = api_format(true, [], [$update], []);
                return response()->json($result);
            }
        } catch (\Throwable $e) {
            $result = api_format(false, $e, [], []);
            return response()->json($result);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        try {
            $post->delete();
            $result = api_format(true, ["Delet Posts"], [], []);
        } catch (\Throwable $e) {
            $result = api_format(false, $e, [], []);
            return response()->json($result);
        }
    }
}
