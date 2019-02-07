<?php

namespace common\base;

use yii\db\ActiveQuery;
use yii;
use yii\caching\TagDependency;

abstract class ActiveRecord extends \yii\db\ActiveRecord
{
    const CACHE_TIME = 10000;

    /**
     * @param integer|array $id
     * @param boolean $asArray
     * @return Object|array|null
     */
    public static function getById($id, $asArray = false, $indexById = false)
    {
        if (!is_array($id)) {
            $idArr = [$id];
        } else {
            $idArr = $id;
        }

        $data = [];

        foreach ($idArr as $key => $value) {

            if ($indexById == true) {
                $arrKey = $value;
            } else {
                $arrKey = $key;
            }

            if ($asArray == true) {

                $data[$arrKey] = Yii::$app->cache->getOrSet(static::className() . $value, function () use ($asArray, $value) {
                    $query = static::find()
                        ->where(['id' => $value])
                        ->asArray();
                    return $query->one();
                }, static::CACHE_TIME);
            } else {
                $data[$arrKey] = static::getDb()->cache(function () use ($value) {
                    return static::find()->where(['id' => $value])->one();
                }, static::CACHE_TIME, new TagDependency(['tags' => static::className() . $value]));
            }
        }

        if (!is_array($id)) {
            return $data[$arrKey];
        }

        return $data;
    }

    /**
     * Сброс кеша создаваемого функцией static::getById()
     * @param $id
     */
    function flushCacheById($id)
    {
        Yii::$app->cache->delete(static::className() . $id);
        TagDependency::invalidate(Yii::$app->cache, static::className() . $id);
    }

    /**
     * @param array $options
     * @return array|Object[]|ActiveQuery
     */
    public static function getAll($options = [])
    {
        $query = static::find();

        if ($options['order']) {
            $query->orderBy($options['order']);
        }

        if ($options['where']) {
            $query->where($options['where']);
        }

        if ($options['andWhere']) {
            foreach ($options['andWhere'] as $condition) {
                $query->andWhere($condition);
            }
        }

        if ($options['orWhere']) {
            foreach ($options['orWhere'] as $condition) {
                $query->orWhere($condition);
            }
        }

        if ($options['with']) {
            $query->with($options['with']);
        }

        if ($options['join']) {
            $query->joinWith($options['join']);
        }

        if ($options['pages']) {
            $query->offset($options['pages']->offset)
                ->limit($options['pages']->limit);
        }

        if ($options['asArray']) {
            $query->asArray();
        }

        if ($options['limit']) {
            $query->limit($options['limit']);
        }

        if ($options['index']) {
            $query->indexBy($options['index']);
        } elseif ($options['index'] !== false) {
            $query->indexBy('id');
        }

        if ($options['group']) {
            $query->groupBy($options['group']);
        }

        if ($options['max']) {
            return $query->max($options['max']);
        }

        if ($options['asQuery']) {
            return $query;
        } else {
            return $query->all();
        }
    }

    public static function getCount($options = [])
    {
        $query = static::find();

        if ($options['where']) {
            $query->where($options['where']);
        }

        if ($options['join']) {
            $query->joinWith($options['join']);
        }

        if ($options['asQuery']) {
            return $query;
        } else {
            return $query->count(static::tableName() . '.[[id]]');
        }
    }
    
    public function save($runValidation = true, $attributeNames = NULL) 
    {
        if (isset($this->id)) {
            static::flushCacheById($this->id);
        }
        
        return parent::save($runValidation, $attributeNames);
    }
    
}
