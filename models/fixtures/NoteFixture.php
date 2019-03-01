<?php

namespace app\models\fixtures;

use app\models\Note\Note;
use yii\test\ActiveFixture;

/**
 * Class NoteFixture.
 *
 * @package common\fixtures
 * @author  Sergey Gubarev <devseregik@gmail.com>
 */
class NoteFixture extends ActiveFixture
{
    public $modelClass = Note::class;

    public $depends = [UserFixture::class];
}