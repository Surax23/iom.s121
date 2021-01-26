<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\model\Groups;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php //print_r($model->errors); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?php $model->auth_key = $model->generateAuthKey(); ?>

    <?php $model->status = 10; ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?php $groups_all = $model->getAllGroups();
    $items = ArrayHelper::map($groups_all,'id','name');
    $params = ['prompt' => 'Выберите роль']; ?>
    <?= $form->field($model, 'groups')->dropDownList($items,$params) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
