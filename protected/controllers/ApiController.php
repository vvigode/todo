<?php
class ApiController extends CController
{
    public $layout = false; // без шаблона

    /**
     * GET /api/list – список задач
     */
    public function actionList()
    {
        $this->ensureTableExists();
        $tasks = Task::model()->findAll(array('order' => 'id DESC'));
        $data = array_map(function($t) { return $t->attributes; }, $tasks);
        $this->renderJson($data);
    }

    /**
     * POST /api/create – добавить задачу
     */
    public function actionCreate()
    {
        $this->ensureTableExists();

        $title = Yii::app()->request->getPost('title');
        $description = Yii::app()->request->getPost('description');
        $image = Yii::app()->request->getPost('image');
        if (!$title) {
            $this->renderJson(array('error' => 'title required'), 400);
        }

        $task = new Task();
        $task->title = $title;
        $task->description = $description;
        $task->image = $image;
        $task->status = 0;
        $task->is_done = 0;
        if ($task->save()) {
            $this->renderJson($task->attributes, 201);
        } else {
            $this->renderJson(array('error' => 'validation', 'details' => $task->getErrors()), 422);
        }
    }

    /**
     * POST /api/toggle – переключить флаг is_done
     * Параметры: id (int), is_done (0|1)
     */
    public function actionToggle()
    {
        $this->ensureTableExists();

        $id = (int)Yii::app()->request->getPost('id');
        $isDone = Yii::app()->request->getPost('is_done');

        if (!$id || $isDone === null) {
            $this->renderJson(['error' => 'id and is_done required'], 400);
        }

        $task = Task::model()->findByPk($id);
        if (!$task) {
            $this->renderJson(['error' => 'task not found'], 404);
        }

        $task->is_done = $isDone ? 1 : 0;
        if ($task->save()) {
            $this->renderJson($task->attributes, 200);
        } else {
            $this->renderJson(['error' => 'validation', 'details' => $task->getErrors()], 422);
        }
    }

    /**
     * POST /api/delete – удалить задачу
     * Параметр: id (int)
     */
    public function actionDelete()
    {
        $this->ensureTableExists();

        $id = (int)Yii::app()->request->getPost('id');
        if (!$id) {
            $this->renderJson(['error' => 'id required'], 400);
        }

        $task = Task::model()->findByPk($id);
        if (!$task) {
            $this->renderJson(['error' => 'task not found'], 404);
        }

        if ($task->delete()) {
            $this->renderJson(['success' => true]);
        } else {
            $this->renderJson(['error' => 'delete failed'], 500);
        }
    }

    /**
     * POST /api/status – изменить статус задачи
     * id, status (0,1,2)
     */
    public function actionStatus()
    {
        $this->ensureTableExists();

        $id = (int)Yii::app()->request->getPost('id');
        $status = (int)Yii::app()->request->getPost('status');
        if (!$id || !in_array($status, [0,1,2])) {
            $this->renderJson(['error' => 'id and valid status required'], 400);
        }

        $task = Task::model()->findByPk($id);
        if (!$task) {
            $this->renderJson(['error' => 'task not found'], 404);
        }

        $task->status = $status;
        $task->is_done = ($status == 2) ? 1 : 0;
        if ($task->save()) {
            $this->renderJson($task->attributes, 200);
        } else {
            $this->renderJson(['error' => 'save failed'], 500);
        }
    }

    /* ===================================================== */

    private function renderJson($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo CJSON::encode($data);
        Yii::app()->end();
    }

    /**
     * При первом запуске создаём таблицу в SQLite, если её нет.
     */
    private function ensureTableExists()
    {
        $db = Yii::app()->db;
        // Проверяем кэш схемы
        $schema = $db->schema->getTable('task', true);
        if ($schema === null) {
            $sql = 'CREATE TABLE task (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT,
                image TEXT,
                status INTEGER DEFAULT 0,
                is_done INTEGER DEFAULT 0
            )';
            $db->createCommand($sql)->execute();
        } else {
            $cols = array_keys($schema->columns);
            if (!in_array('description', $cols)) {
                $db->createCommand('ALTER TABLE task ADD COLUMN description TEXT')->execute();
            }
            if (!in_array('image', $cols)) {
                $db->createCommand('ALTER TABLE task ADD COLUMN image TEXT')->execute();
            }
            if (!in_array('status', $cols)) {
                $db->createCommand('ALTER TABLE task ADD COLUMN status INTEGER DEFAULT 0')->execute();
            }
        }
    }
} 