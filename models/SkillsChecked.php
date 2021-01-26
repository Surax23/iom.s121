<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "skills_checked".
 *
 * @property int $id
 * @property int|null $value
 * @property int $students_id
 * @property int $skills_id
 * @property int $user_id
 *
 * @property Skills $skills
 * @property Students $students
 * @property User $user
 */
class SkillsChecked extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'skills_checked';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value', 'students_id', 'skills_id', 'user_id'], 'integer'],
            [['students_id', 'skills_id', 'user_id'], 'required'],
            [['skills_id'], 'exist', 'skipOnError' => true, 'targetClass' => Skills::className(), 'targetAttribute' => ['skills_id' => 'id']],
            [['students_id'], 'exist', 'skipOnError' => true, 'targetClass' => Students::className(), 'targetAttribute' => ['students_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => 'Значение',
            'students_id' => 'Студент',
            'skills_id' => 'Навык',
            'user_id' => 'Сотрудник',
        ];
    }

    /**
     * Gets query for [[Skills]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSkills()
    {
        return $this->hasOne(Skills::className(), ['id' => 'skills_id']);
    }

    /**
     * Gets query for [[Students]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasOne(Students::className(), ['id' => 'students_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getSkillsByStudent($id)
    {
        return $this->hasMany(Skills::className(), [$id => 'students_id']);
    }
}
