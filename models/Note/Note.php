<?php

namespace app\models\Note;

use app\models\User;
use cornernote\softdelete\SoftDeleteBehavior;
use yii\base\InvalidValueException;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%note}}".
 *
 * @property int    $id
 * @property int    $user_id
 * @property string $title
 * @property string $text
 * @property int    $created_at
 * @property int    $updated_at
 * @property int    $published_at
 * @property int    $deleted_at
 *
 * @property User   $user
 *
 * @mixin SoftDeleteBehavior
 *
 * @package app\models\Note
 * @author  Sergey Gubarev <devseregik@gmail.com>
 */
class Note extends \yii\db\ActiveRecord
{
    /**
     * @var string Scenario view model data.
     */
    const SCENARIO_VIEW = 'scenarioView';

    /**
     * @var string Scenario for save model.
     */
    const SCENARIO_SAVE = 'scenarioSave';


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (\Yii::$app->id !== 'console') {
            $this->user_id = \Yii::$app->user->id;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%note}}';
    }

    /**
     * @see SoftDeleteTrait::getDeletedAtAttribute()
     */
    public static function getDeletedAtAttribute() {
        return  'deleted_at';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['text', 'title', 'user_id'], 'required'],
            [['text', 'title'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['user_id', 'published_at', 'created_at', 'updated_at', 'deleted_at'], 'integer'],
            ['deleted_at', 'default', 'value' => null],
            ['published_at', 'default', 'value' => time()],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        return [
            self::SCENARIO_SAVE => ['title', 'text', 'published_at'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'timestamps' => TimestampBehavior::class,
            'softDelete' => SoftDeleteBehavior::class,
        ];
    }

    /**
     * Handler on beforeValidate.
     *
     * @return bool
     *
     * @throws \yii\base\InvalidValueException
     */
    public function beforeValidate(): bool
    {
        if (parent::beforeValidate()) {
            $this->published_at = is_numeric($this->published_at) ? $this->published_at : strtotime($this->published_at);
            if (!$this->published_at) {
                throw new InvalidValueException('Invalid published date.');
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'         => 'ID',
            'user_id'    => 'User ID',
            'title'      => 'Title',
            'text'       => 'Text',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields = ['title', 'published_at', 'user'];
        if ($this->scenario === self::SCENARIO_VIEW) {
            $fields[] = 'text';
        }
        return $fields;
    }

    /**
     * Сheck whether the note is deleted.
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted_at !== null;
    }

    /**
     * Сheck whether the note is actual.
     *
     * Indicates that the publication date has already come.
     *
     * @return bool
     */
    public function isActual(): bool
    {
        return $this->published_at <= time();
    }

    /**
     * {@inheritdoc}
     *
     * @return NoteQuery the active query used by this AR class.
     */
    public static function find(): NoteQuery
    {
        return new NoteQuery(static::class);
    }
}
