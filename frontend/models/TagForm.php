<?php
namespace frontend\models;

use common\models\TagModel;
use yii\base\Model;

/**
 * 标签的表单模型
 */

class TagForm extends Model
{
    public $id;
    public $tags;

    public function rules()
    {
        return[
            ['tags','required'],
            ['tags','each','rule'=>['string']],
        ];
    }

    /**
     * 保存标签集合
     * @return array
     */

    public function saveTags()
    {
        $ids = [];
        if(!empty($this->tags)){
            foreach($this->tags as $tag){
                $ids[] = $this->_saveTag($tag);
            }
        }
        return $ids;
    }

    /**
     * 保存标签
     * @return array
     */
    private function _saveTag($tag)
    {
        $model = new TagModel();
        $res = $model->find()->where(['tag_name'=>$tag])->one();
        //新建标签
        if(!$res){
            $model->tag_name = $tag;
            $model->post_num = 1;
            if(!$model->save()){
                throw new \Exception("保存标签失败！");
            }
            return $model->id;
        }else{
            $res -> updateCounters(['post_num'=>1]);
        }
        return $res->id;
    }


}