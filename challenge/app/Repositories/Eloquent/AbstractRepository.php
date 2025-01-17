<?php

namespace App\Repositories\Eloquent;

abstract class AbstractRepository
{
    protected $model;

    protected $wallet;

    public function __construct()
    {
        $this->model = $this->resolveModel();
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function whereEquals($column, $value)
    {
        return $this->model->where($column, $value);
    }

    public function first()
    {
        $this->model->first();
    }

    public function findWallet(int $id)
    {
        return $this->wallet::findOrFail($id);
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update(array $data)
    {
        return $this->model->update($data);
    }

    public function paginate(int $integer)
    {
        return $this->model->paginate($integer);
    }

    public function paginateById(int $id, int $integer)
    {
        return $this->model->where('wallet_id', $id)->paginate($integer);
    }

    public function orderBy($column, $clause = 'DESC')
    {
        return $this->model->orderBy($column, $clause);
    }

    public function delete()
    {
        return $this->model->delete();
    }


    protected function resolveModel()
    {
        return app($this->model);
    }
}
