<?php

namespace app\models\fixtures;

use app\models\User;
use yii\test\ActiveFixture;

/**
 * Class UserFixture.
 *
 * @package common\fixtures
 * @author  Sergey Gubarev <devseregik@gmail.com>
 */
class UserFixture extends ActiveFixture
{
    public $modelClass = User::class;
}