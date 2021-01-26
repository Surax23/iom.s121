<?php

namespace app\models;

use Yii;
use app\models\Age;

/**
 * This is the model class for table "students".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $class
 * @property string|null $birthday
 * @property int $age_id
 *
 * @property SkillsChecked[] $skillsCheckeds
 * @property StudentCheckByTeacher[] $studentCheckByTeachers
 * @property Age $age
 */
class Students extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'students';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['birthday'], 'safe'], YYYY-MM-DD
            [['birthday'], 'date', 'format' => 'php:Y-m-d'],
            [['age_id'], 'required'],
            [['age_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['class'], 'string', 'max' => 45],
            [['age_id'], 'exist', 'skipOnError' => true, 'targetClass' => Age::className(), 'targetAttribute' => ['age_id' => 'id']],
            ['age_id', 'default', 'value' => 0],
            ['age_id', 'in', 'range' => [0, 1, 2, 3, 4, 5, 6]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'ФИО',
            'class' => 'Класс',
            'birthday' => 'Год рождения',
            'age_id' => 'Возрастная группа',
        ];
    }

    /**
     * Gets query for [[SkillsCheckeds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSkillsCheckeds()
    {
        return $this->hasMany(SkillsChecked::className(), ['students_id' => 'id']);
    }

    /**
     * Gets query for [[StudentCheckByTeachers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudentCheckByTeachers()
    {
        return $this->hasMany(StudentCheckByTeacher::className(), ['students_id' => 'id']);
    }

    /**
     * Gets query for [[Age]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAge()
    {
        return $this->hasOne(Age::className(), ['id' => 'age_id']);
    }

    public function getAllAge()
    {
        return Age::find()->all();
    }

    public function afterFind()
    {
        $this->birthday = Yii::$app->formatter->asDatetime($this->birthday,'php:d.m.Y');

        return parent::afterFind();
    }

    public function beforeValidate()
    {
        $this->birthday = Yii::$app->formatter->asDate($this->birthday,'php:Y-m-d');//Yii::$app->formatter->asDatetime($this->birthday,'php:Y-m-d');

        return parent::beforeValidate();
    }
}
