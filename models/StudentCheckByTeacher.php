<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "student_check_by_teacher".
 *
 * @property int $id
 * @property int $num
 * @property string|null $status
 * @property int $students_id
 * @property int|null $user_id
 *
 * @property SkillsChecked[] $skillsCheckeds
 * @property Students $students
 * @property User $user
 */
class StudentCheckByTeacher extends \yii\db\ActiveRecord
{
    public const STATUS_APPOINTED = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_FINISHED = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student_check_by_teacher';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['students_id'], 'required'],
            [['students_id', 'user_id'], 'integer'],
            [['status', 'stage'], 'integer'],
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
            'status' => 'Статус',
            'students_id' => 'ID ученика',
            'user_id' => 'ID пользователя',
            'stage' => 'Этап'
        ];
    }

    /**
     * Gets query for [[SkillsCheckeds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSkillsCheckeds()
    {
        return $this->hasMany(SkillsChecked::className(), ['attempt' => 'id']);
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
}
