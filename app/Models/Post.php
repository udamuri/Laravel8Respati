<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body'
    ];

    public $timestamps = true;

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->where('posts.title', 'LIKE', '%'.$term.'%')
            ->orWhere('posts.body', 'LIKE', '%'.$term.'%');
        }
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);
        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public static function createModel($request) {
        $data = $request->only([
            'title',
            'body'
        ]);
        
        $result = self::create($data);

        return $result;
    }

    public function updateModel($request) {
        $data = $request->only([
            'title',
            'body'
        ]);
        
        $this->update($data);

        $result = self::find($this->id);
        return $result;
    }
}
