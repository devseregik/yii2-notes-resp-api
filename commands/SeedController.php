<?php

namespace app\commands;

use app\models\Note\Note;
use app\models\User;
use Faker\Factory;
use Faker\Generator;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Class SeedController.
 *
 * @package console\controllers
 * @author  Sergey Gubarev <devseregik@gmail.com>
 */
class SeedController extends Controller
{
    /**
     * @var int Generate users count.
     */
    public $usersCount = 2;

    /**
     * @var Generator;
     */
    private $_faker;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->_faker = Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function optionAliases(): array
    {
        return ['u' => 'usersCount'];
    }

    /**
     * {@inheritdoc}
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'usersCount'
        ]);
    }

    /**
     * Seeding db.
     *
     * The command "yii seed" will generates two users with notes.
     * The command "yii seed -u=3" will generates three users with notes.
     *
     * @return int
     */
    public function actionIndex(): int
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $this->stdout("Seeding database...\n", Console::FG_GREY);

            // Truncate tables before
            \Yii::$app->db->createCommand('TRUNCATE ' . implode(', ', [User::tableName(), Note::tableName()]) . ' RESTART IDENTITY CASCADE')->execute();

            // Generates users
            for ($u = 1; $u <= $this->usersCount; $u++) {
                $user = new User();
                $user->username = $this->_faker->userName;
                $user->email = $this->_faker->email;
                $user->access_token = \Yii::$app->getSecurity()->generateRandomString();
                $user->password_hash = \Yii::$app->getSecurity()->generatePasswordHash('password' . $u);
                $user->password_reset_token = \Yii::$app->getSecurity()->generateRandomString();
                $user->save(false);
            }

            // Generates users notes
            $users = User::find()->all();
            foreach ($users as $user) {
                for ($n = 1; $n <= 2; $n++) {
                    $note = $this->makeNote();
                    $note->link('user', $user);
                }

                $pastNote = $this->makeNote(strtotime('-2 day'), true);
                $pastNote->link('user', $user);

                $featureNote = $this->makeNote(strtotime('+2 day'));
                $featureNote->link('user', $user);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();

            \Yii::error($e->getTraceAsString());
            $this->stdout($e->getMessage(), Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("OK\n", Console::FG_GREY, Console::BG_GREEN);
        return ExitCode::OK;
    }

    /**
     * Make a note entity by special time.
     *
     * @param int|null $time
     * @param bool     $isPastTime
     * @return Note
     */
    protected function makeNote(?int $time = null, bool $isPastTime = false): Note
    {
        $note = new Note();
        $note->title = $this->_faker->text(20);
        $note->text = $this->_faker->text;
        $note->published_at = time();

        if ($time) {
            if ($isPastTime) {
                $note->detachBehavior('timestamps');
                $note->created_at = $time;
                $note->updated_at = $time;
            }

            $note->published_at = $time;
        }

        return $note;
    }
}