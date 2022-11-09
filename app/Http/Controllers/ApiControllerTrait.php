<?php
namespace  App\Http\Controllers;

use Illuminate\Http\Request;

trait ApiControllerTrait
{
    public function index(Request $request)
    {
        $limit = $request->all()['limit'] ?? 20;
        $order = $request->all()['order'] ?? null;
        if ($order !== null){
            $order = explode(',', $order);
        }

        $oder[0] = $order[0] ?? 'id';
        $oder[1] = $order[1] ?? 'asc';

        $where = $request->all()['where'] ?? [];
        $like = $request->all()['like'] ?? null;
        if($like){
            $like = explode(',', $like);
            $like[1] = '%'.$like[1] .'%';
        }
        $result = $this->model->orderby($oder[0],$oder[1])
            ->where(function ($query) use($like){
                if($like){
                    return $query->where($like[0], 'like', $like[1]);
                }
                return $query;
            })
            ->where($where)
            ->with($this->relationships())
            ->paginate($limit);

        return response()->json($result);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = $this->model->create($request->all());
        return response()->json($result, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = $this->model
            ->with($this->relationships)
            ->findOrFail($id);
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $result = $this->model->findOrFail($id);
        $result->update($request->all());
        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->model->findOrFail($id);
        $result->delete();
        return response()->json($result);
    }

    protected function relationships()
    {
        if(isset($this->relationships)){
            return $this->relationships;
        }
        return [];
    }
}
