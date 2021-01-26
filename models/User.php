<?php

namespace app\models;

use Yii;
use Yii\base\NotSupportedException;
use Yii\behaviors\TimestampBehavior;
use Yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use app\models\Groups;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string|null $auth_key
 * @property string|null $password_reset_token
 * @property int|null $status
 * @property string|null $name
 * @property string|null $password_hash
 * @property int $groups
 *
 * @property SkillsChecked[] $skillsCheckeds
 * @property StudentCheckByTeacher[] $studentCheckByTeachers
 * @property Groups $groups0
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $password;

    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 1;

    const ROLE_ADMIN = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'groups'], 'required'],
            [['id', 'status', 'groups'], 'integer'],
            [['username', 'name'], 'string', 'max' => 100],
            [['auth_key'], 'string', 'max' => 32],
            [['password_reset_token'], 'string', 'max' => 80],
            [['password_hash'], 'string', 'max' => 256],
            [['password_reset_token'], 'unique'],
            [['id'], 'unique'],
            [['groups'], 'exist', 'skipOnError' => true, 'targetClass' => Groups::className(), 'targetAttribute' => ['groups' => 'id']],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['groups', 'default', 'value' => 0],
            ['groups', 'in', 'range' => [0, self::ROLE_ADMIN, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Имя пользователя',
            'auth_key' => 'Auth Key',
            'password_reset_token' => 'Токен сброса пароля',
            'status' => 'Статус',
            'name' => 'ФИО',
            'password_hash' => 'Пароль',
            'groups' => 'Группа',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->password= $this->setPassword($this->password);
            return parent::beforeSave($insert);
        } else {
            return false;
        }
    }

    /**
     * Gets query for [[SkillsCheckeds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSkillsCheckeds()
    {
        return $this->hasMany(SkillsChecked::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[StudentCheckByTeachers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudentCheckByTeachers()
    {
        return $this->hasMany(StudentCheckByTeacher::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Groups0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroups0()
    {
        return $this->hasOne(Groups::className(), ['id' => 'groups']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }
 
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }
 
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }
 
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
 
    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }
 
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
 
    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
 
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }
 
    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function getAllGroups()
    {
        return Groups::find()->all();
    }

    public function getGroupsLink()
    {
        return $this->hasMany(Groups::className(), ['id' => 'groups']);
    }

    public function getGroup() {
        //return Groups::findOne($id)->name;
        return $this->hasOne(Groups::className(), ['id' => 'groups']);
    }

    public function getGroups() {
        //return Groups::find()->all();
        return $this->hasOne(Groups::className(), ['id' => 'groups']);
    }

    public static function isUserAdmin($id)
    {
          if (static::findOne(['id' => $id, 'groups' => self::ROLE_ADMIN])){
                            
                 return true;
          } else {
                            
                 return false;
          }
            
    }
}
