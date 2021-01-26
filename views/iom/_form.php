<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Skills */
/* @var $form ActiveForm */
?>
<div class="iom-_form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'dev_dir_id') ?>
        <?= $form->field($model, 'skill_name') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- iom-_form -->
