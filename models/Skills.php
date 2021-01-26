<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "skills".
 *
 * @property int $id
 * @property string|null $skill_name
 * @property int $dev_dir_id
 *
 * @property DevDir $devDir
 * @property SkillsChecked[] $skillsCheckeds
 */
class Skills extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'skills';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dev_dir_id'], 'required'],
            [['dev_dir_id'], 'integer'],
            [['skill_name'], 'string', 'max' => 45],
            [['dev_dir_id'], 'exist', 'skipOnError' => true, 'targetClass' => DevDir::className(), 'targetAttribute' => ['dev_dir_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'skill_name' => 'Навык',
            'dev_dir_id' => 'Dev Dir ID',
        ];
    }

    /**
     * Gets query for [[DevDir]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDevDir()
    {
        return $this->hasOne(DevDir::className(), ['id' => 'dev_dir_id']);
    }

    /**
     * Gets query for [[SkillsCheckeds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSkillsCheckeds()
    {
        return $this->hasMany(SkillsChecked::className(), ['skills_id' => 'id']);
    }

    public function getSkillsByAge() {
        return $this->hasMany(SkillsByAge::className(), ['skill_id' => 'id']);
    }

    public function getSkillsByGroup() {
        return $this->hasMany(SkillsByGroup::className(), ['skill_id' => 'id']);
    }
}
