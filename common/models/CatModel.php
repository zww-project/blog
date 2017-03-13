<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cats".
 *
 * @property integer $id
 * @property string $cat_name
 */
class CatModel extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cats';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cat_name' => 'Cat Name',
        ];
    }

    /**
     * 获取所有分类
     */
    public static function getAllCats(){
        $cat = ['0'=>'暂无分类'];
        $res = self::find()->asArray()->all();
        if($res){
            foreach($res as $cat_v){
                $cat[$cat_v['id']] = $cat_v['cat_name'];
            }
        }
        return $cat;
    }
}
