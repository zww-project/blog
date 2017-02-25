<?php
namespace frontend\models;
use yii\base\Model;
/**
 * 文章表单模型
 * User: zww
 * Date: 2017/1/7
 */
class PostForm extends Model
{
    public $id;
    public $title;
    public $content;
    public $label_img;
    public $cat_id;
    public $tags;
    public $_lastError = "";

    public function rules()
    {
        return[
            [['id','title','cat_id'],'required'],
            [['id','cat_id'],'integer'],
            ['title','string','min'=>4,'max'=>50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'=>'编码',
            'title'=>'标题',
            'content'=>'内容',
            'label_img'=>'标签图',
            'tags'=>'标签',
            'cat_id'=>'分类',
        ];
    }
}