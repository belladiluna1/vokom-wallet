<?php
  use yii\helpers\Html;
  use yii\widgets\ActiveForm;
?>

<h1>Совершить перевод средств</h1>

<?php $form = ActiveForm::begin(); ?>
  <?= $form->field($model, 'senderId')->dropDownList($clientsList) ?>
  <?= $form->field($model, 'recepientId')->dropDownList($clientsList) ?>
  <?= $form->field($model, 'value') ?>
  <div class="form-group">
  <?= Html::submitButton('Отправить перевод', ['class' => 'btn btn-primary'])
  ?>
<?php ActiveForm::end(); ?>

