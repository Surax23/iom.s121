<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "skills_by_age".
 *
 * @property int $id
 * @property int $skill_id
 * @property int $age_id
 *
 * @property Age $age
 * @property Skills $skill
 */
class SkillsByAge extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'skills_by_age';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['skill_id', 'age_id'], 'required'],
            [['skill_id', 'age_id'], 'integer'],
            [['age_id'], 'exist', 'skipOnError' => true, 'targetClass' => Age::className(), 'targetAttribute' => ['age_id' => 'id']],
            [['skill_id'], 'exist', 'skipOnError' => true, 'targetClass' => Skills::className(), 'targetAttribute' => ['skill_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'skill_id' => 'Skill ID',
            'age_id' => 'Age ID',
        ];
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

    /**
     * Gets query for [[Skill]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSkill()
    {
        return $this->hasOne(Skills::className(), ['id' => 'skill_id']);
    }
}
