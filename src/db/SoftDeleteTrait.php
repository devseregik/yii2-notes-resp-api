<?php

namespace app\src\db;

use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;

/**
 * Trait SoftDeleteTrait
 *
 * @package app\src\db
 */
trait SoftDeleteTrait
{
    public function delete()
    {
        $deletedAtAttribute = self::getDeletedAtAttribute();

        if(!empty($this->$deletedAtAttribute)) {
            return $this->hardDelete();
        } else {
            return $this->softDelete();
        }
    }

    public function softDelete()
    {
        $this->beforeSoftDelete();

        $attributes = self::deleteAttributeValues();
        foreach ($attributes as $attribute => $value) {
            $this->{$attribute} = $value;
        }

        $result = $this->save(false, array_keys(self::deleteAttributeValues()));

        $this->afterSoftDelete();

        return $result;
    }

    public function hardDelete()
    {
        return parent::delete() !== false;
    }

    public function beforeSoftDelete()
    {
        // Default implementation
    }

    public function afterSoftDelete()
    {
        // Default implementation
    }

    public function restore()
    {
        $attributes = self::restoreAttributeValues();
        foreach ($attributes as $attribute => $value) {
            $this->$attribute = $value;
        }

        $result = $this->save(false, array_keys(self::deleteAttributeValues()));

        return $result;
    }

    /**
     * Finds where deleted_at IS NULL
     *
     * @param boolean $withDeleted
     *
     * @return \yii\db\Query|ActiveQuery|ActiveQueryInterface The newly created yii\db\ActiveQueryInterface instance.
     * @throws InvalidConfigException
     */
    public static function find($withDeleted = true) {

        if($withDeleted) {
            return parent::find();
        } else {
            return parent::find()->andWhere(['IS', self::tableName() . '.' . self::getDeletedAtAttribute(), null]);
        }
    }

    /**
     * Finds where deleted_at IS NOT NULL
     *
     * @return mixed|\yii\db\Query The newly created yii\db\ActiveQueryInterface instance.
     * @throws InvalidConfigException
     */
    public static function findDeleted() {
        return parent::find()->andWhere(self::tableName() . '.' . self::getDeletedAtAttribute() . ' IS NOT NULL');
    }

    /**
     * Finds records regardless of deleted_at
     *
     * @return \yii\db\Query The newly created yii\db\ActiveQueryInterface instance.
     * @throws InvalidConfigException
     */
    public static function withDeleted() {
        return parent::find();
    }

    public static function getDeletedAtAttribute()
    {
        return 'deleted_at';
    }

    public static function deleteAttributeValues() :array
    {
        return [
            'deleted_at' => time()
        ];
    }

    public static function restoreAttributeValues() :array
    {
        return [
            'deleted_at' => null
        ];
    }

    public function isDeleted() :bool
    {
        if($this->{self::getDeletedAtAttribute()}) {
            return true;
        }

        return false;
    }
}