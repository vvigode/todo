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
        if (!$title) {
            $this->renderJson(array('error' => 'title required'), 400);
        }

        $task = new Task();
        $task->title = $title;
        $task->is_done = 0;
        if ($task->save()) {
            $this->renderJson($task->attributes, 201);
        } else {
            $this->renderJson(array('error' => 'validation', 'details' => $task->getErrors()), 422);
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
                is_done INTEGER DEFAULT 0
            )';
            $db->createCommand($sql)->execute();
        }
    }
} 