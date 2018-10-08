<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
?>

<div class="radiometric-target-form well">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?php 
    if ($model->isNewRecord) {
        echo $form->field($model, 'uri')->textInput([
                'maxlength' => true, 
                'readonly' => true, 
                'id' => 'experimentURI', 
                'value' => Yii::$app->params['baseURI'],
                'style' => 'background-color:#C4DAE7;',
                'data-toggle' => 'tooltip',
                'title' => 'Automatically generated',
                'data-placement' => 'left'
            ]);
    } else {
        echo $form->field($model, 'uri')->textInput([
                'readonly' => true, 
                'style' => 'background-color:#C4DAE7;',
            ]);
    }
    ?>

    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'brand')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'serialNumber')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'inServiceDate')->widget(\kartik\date\DatePicker::className(), [
        'options' => [
            'placeholder' => Yii::t('app', 'Enter in service date'), 
//            'onChange' => 'updateURI()',
            'id' => 'rtInServiceDate'],            
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ]) ?>
    
    <?= $form->field($model, 'dateOfPurchase')->widget(\kartik\date\DatePicker::className(), [
        'options' => [
            'placeholder' => Yii::t('app', 'Enter date of purchase'), 
//            'onChange' => 'updateURI()',
            'id' => 'rtDateOfPurchase'],            
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ]) ?>
    
    <?= $form->field($model, 'dateOfLastCalibration')->widget(\kartik\date\DatePicker::className(), [
        'options' => [
            'placeholder' => Yii::t('app', 'Enter date of last calibration'), 
//            'onChange' => 'updateURI()',
            'id' => 'rtDateOfLastCalibration'],            
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ]) ?>
    
    <?= $form->field($model, 'personInCharge')->widget(\kartik\select2\Select2::classname(),[
            'data' => $this->params['listContacts'],
            'options' => [
                'value' => [],
            ],
            'pluginOptions' => [
                'allowClear' => false
            ],
        ]);
    ?>
    
    <?= $form->field($model, 'material')->widget(\kartik\select2\Select2::classname(),[
            'data' => [
                'carpet' => Yii::t('app', 'Carpet'),
                'painting' => Yii::t('app', 'Painting'),
                'spectralon' => Yii::t('app', 'Spectralon')
            ],
            'options' => [
                'value' => [],
            ],
            'pluginOptions' => [
                'allowClear' => false
            ],
        ]);
    ?>
    
    <script>
        function rtChangeFigure(value) {
            if (value === 'circular') {
                $(".field-yiiradiometrictargetmodel-diameter").show();
                $(".field-yiiradiometrictargetmodel-width").hide();
                $(".field-yiiradiometrictargetmodel-length").hide();
            } else {
                $(".field-yiiradiometrictargetmodel-diameter").hide();
                $(".field-yiiradiometrictargetmodel-width").show();
                $(".field-yiiradiometrictargetmodel-length").show();
                
            }
        }
        
        $(document).ready(function() {
            rtChangeFigure($('#yiiradiometrictargetmodel-shape').val())
        });
    </script>
    <?= $form->field($model, 'shape')->widget(\kartik\select2\Select2::classname(),[
            'data' => [
                'rectangular' => Yii::t('app', 'Rectangular'),
                'circular' => Yii::t('app', 'Circular')
            ],
            'options' => [
                'value' => [],
            ],
            'pluginOptions' => [
                'allowClear' => false
            ],
            'pluginEvents' => [
                "change" => "function(event) { rtChangeFigure(event.target.value)}",
             ]
        ]);
    ?>

    <?= $form->field($model, 'length')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'width')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'diameter')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'brdfP1')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'brdfP2')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'brdfP3')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'brdfP4')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'reflectanceFile')->widget(FileInput::classname(), [
            'options'=>[
                'multiple'=>false,
            ],
            'pluginOptions' => [               
                'maxFileCount' => 1,
                'maxFileSize'=>2000
            ]
        ]);
     ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('yii', 'Create') : Yii::t('yii', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

