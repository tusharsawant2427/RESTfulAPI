<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

trait ApiResponser
{
    private function successReponse($data, $code)
    {
        return response()->json($data, $code);
    }
    protected function errorResponse($message, $code)
    {
        return response()->json(['error'=>$message, 'code'=>$code], $code);
    }

    protected function showAll(Collection  $collection, $code = 200)
    {
        if($collection->isEmpty()) {
            return $this->successReponse(['data'=>$collection], $code);
        }
        $transformer = $collection->first()->transformer;
        $collection = $this->filterData($collection, $transformer);
        $collection = $this->sort($collection, $transformer);
        $collection = $this->paginate($collection);
        $collection = $this->transformData($collection, $transformer);
        $collection = $this->cacheResponse($collection);

        return $this->successReponse($collection, $code);
    }
    protected function showOne(Model $model, $code = 200)
    {
        $transformer = $model->transformer;
        $model = $this->transformData($model, $transformer);

        return $this->successReponse([$model], 200);
    }

    protected function showMessage($message, $code=200)
    {
        return $this->successReponse(['data' => $message], $code);
    }

    protected function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer);
        return $transformation->toArray();
    }

    private function sort(Collection $collection, $transformer)
    {
        if(request()->has('sort_by')) {
            $transformedAttribute = request()->sort_by;
            $sortAttribute = $transformer::attributeMapper($transformedAttribute);
            $collection = $collection->sortBy($sortAttribute);
        }

        return $collection;
    }

    private function filterData(Collection $collection, $transformer)
    {
        foreach (request()->query() as $query => $value)
        {
            $actualAttribute = $transformer::attributeMapper($query);
            if(isset($actualAttribute, $value)) {
                $collection = $collection->where($actualAttribute, $value);

            }
        }

        return $collection;
    }

    private function paginate(Collection $collection)
    {
        $rules = [
            'per_page' => 'integer|min:10|max:100'
        ];

        Validator::validate(request()->all(), $rules);


        $page = LengthAwarePaginator::resolveCurrentPage();
        $elementsPerPage = 15;

        if(request()->has('per_page')) {
            $elementsPerPage = (int)request()->get('per_page');
        }

        $results = $collection->slice($elementsPerPage * ($page-1), $elementsPerPage)->values();
        $paginator = new LengthAwarePaginator($results, $collection->count(), $elementsPerPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);

        $paginator->appends(request()->all()); // to keep the existing querystring and its values
        return $paginator;
    }

    private function cacheResponse(mixed $data)
    {
        $url = request()->url();
        $queryParams = request()->query();

        ksort($queryParams);

        $queryString = http_build_query($queryParams);

        $fullUrl = "{$url}?{$queryString}";
        return Cache::remember($fullUrl, 30, function () use($data) {
            return $data;
        });
    }
}
