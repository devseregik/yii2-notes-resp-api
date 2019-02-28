<?php

namespace app\models\Note;

/**
 * This is the ActiveQuery class for [[Note]].
 *
 * @see Note
 *
 * @package app\models\Note
 * @author  Sergey Gubarev <devseregik@gmail.com>
 */
class NoteQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Notes[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Notes|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
