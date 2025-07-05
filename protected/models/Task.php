<?php
class Task extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'task';
    }

    public function rules()
    {
        return array(
            array('title', 'required'),
            array('title', 'length', 'max' => 255),
            array('description, image', 'safe'),
            array('status', 'numerical', 'integerOnly' => true),
            array('is_done', 'boolean'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'title' => 'Название',
            'description' => 'Описание',
            'image' => 'Картинка',
            'status' => 'Статус',
            'is_done' => 'Выполнено',
        );
    }

    protected function beforeSave()
    {
        if ($this->isNewRecord && $this->is_done === null) {
            $this->is_done = 0;
        }
        return parent::beforeSave();
    }
} 