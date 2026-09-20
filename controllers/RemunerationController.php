<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../models/Remuneration.php';
class RemunerationController
{
    private Remuneration $model;
    public function __construct() { $this->model = new Remuneration(database()); }
    public function all(): array { return $this->model->all(); }
    public function formateurs(): array { return $this->model->formateurs(); }
    public function find(int $id): ?array { return $this->model->find($id); }
    public function save(array $data): bool { return $this->model->save($data); }
    public function delete(int $id): bool { return $this->model->delete($id); }
}