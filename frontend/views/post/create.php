<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
$this->title = '新建';
$this->params['breadcrumbs'][] = ['label'=>'文章','url'=>['post/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-9">
        <div class="panel-title box-title">
            <span>创建文章</span>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin()?>
            <?=$form->field($model,'title')->textinput(['maxlength'=>true])?>
            <?=$form->field($model,'cat_id')->dropDownList(['1'=>'php','2'=>'mysql','3'=>'redis'])?>
            <?=$form->field($model,'content')->textinput(['maxlength'=>true])?>
            <?=$form->field($model,'label_img')->textinput(['maxlength'=>true])?>
            <?=$form->field($model,'tags')->textinput(['maxlength'=>true])?>
            <div class="form-group">
                <?=Html::submitButton("发布",['class'=>'btn btn-success'])?>
            </div>
            <?php $form = ActiveForm::end()?>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="panel-title box-title">
            <span>热门文章</span>
        </div>
    </div>

</div>
