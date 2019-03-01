<?php

namespace api\tests\api;

use ApiTester;
use app\controllers\NoteController;
use app\models\fixtures\NoteFixture;
use app\models\fixtures\UserFixture;
use app\models\Note\Note;
use app\models\User;

/**
 * Class NotesCest.
 *
 * @package api\tests\api
 * @author  Sergey Gubarev <devseregik@gmail.com>
 */
class NotesCest
{
    public function _fixtures()
    {
        return [
            'user' => [
                'class'    => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
            'note' => [
                'class'    => NoteFixture::class,
                'dataFile' => codecept_data_dir() . 'note.php',
            ],
        ];
    }

    private function _indexSeeResponse(ApiTester $I, $hasValidPage = true): void
    {
        $I->seeResponseCodeIs(200);

        $I->seeResponseJsonMatchesJsonPath('$.count');
        $I->seeResponseJsonMatchesJsonPath('$.pageCount');
        $I->seeResponseJsonMatchesJsonPath('$.currentPage');

        if ($hasValidPage) {
            $I->seeResponseJsonMatchesJsonPath('$.notes..title');
            $I->seeResponseJsonMatchesJsonPath('$.notes..published_at');
            $I->seeResponseJsonMatchesJsonPath('$.notes..user');
        }
    }

    public function index(ApiTester $I): void
    {
        $I->sendGET('/api/notes');

        $this->_indexSeeResponse($I);

        $json = \GuzzleHttp\json_decode($I->grabResponse());
        $I->assertEquals($json->pageCount, 2);
        $I->assertEquals($json->currentPage, 1);
        $I->assertCount($json->count, $I->grabFixture('note'));
        $I->assertCount(NoteController::PER_PAGE, $json->notes);
    }

    public function indexWithPage(ApiTester $I): void
    {
        $page = 2;
        $I->sendGET('/api/notes', ['p' => $page]);

        $this->_indexSeeResponse($I);

        $json = \GuzzleHttp\json_decode($I->grabResponse());
        $I->assertEquals($json->pageCount, 2);
        $I->assertEquals($json->currentPage, $page);
        $I->assertCount($json->count, $I->grabFixture('note'));
    }

    public function indexWithInvalidPage(ApiTester $I): void
    {
        $page = 100;
        $I->sendGET('/api/notes', ['p' => $page]);

        $this->_indexSeeResponse($I, false);

        $json = \GuzzleHttp\json_decode($I->grabResponse());
        $I->assertEquals($json->pageCount, 2);
        $I->assertEquals($json->currentPage, $page);
        $I->assertCount(0, $json->notes);
    }

    public function view(ApiTester $I): void
    {
        $I->sendGET('/api/notes/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'title' => 'First note title',
            'text'  => 'First note text',
            'user'  => [
                'id'        => 1,
                'username'  => 'user_1',
                'email'     => 'user_1@user.com',
                'is_active' => true,
            ],
        ]);
    }

    public function viewHasBeenDeleted(ApiTester $I): void
    {
        $I->sendGET('/api/notes/2');
        $I->seeResponseCodeIs(403);
    }

    public function viewWithFuturePublicationDate(ApiTester $I): void
    {
        $I->sendGET('/api/notes/3');
        $I->seeResponseCodeIs(403);
    }

    public function createUnauthorized(ApiTester $I): void
    {
        $I->sendPOST('/api/notes', [
            'title' => 'New note title',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function create(ApiTester $I): void
    {
        /**
         * @var User $user
         */
        $user = $I->grabFixture('user', 0);
        $I->amBearerAuthenticated($user->access_token);
        $I->sendPOST('/api/notes', [
            'title'        => 'New note title',
            'text'         => 'New note text',
            'published_at' => '25.05.2019 11:44:22',
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson([
            'title'        => 'New note title',
            'published_at' => strtotime('25.05.2019 11:44:22'),
            'user'         => [
                'id' => $user->id,
            ],
        ]);
    }

    public function updateUnauthorized(ApiTester $I): void
    {
        $I->sendPUT('/api/notes/1', [
            'title' => 'Updated note title',
        ]);
        $I->seeResponseCodeIs(401);
    }

    public function updateByNotOwner(ApiTester $I): void
    {
        /**
         * @var User $user
         */
        $user = $I->grabFixture('user', 1);
        $I->amBearerAuthenticated($user->access_token);
        $I->sendPUT('/api/notes/3', [
            'title' => 'Updated note title',
        ]);
        $I->seeResponseCodeIs(403);
    }

    public function updateWitchCreatedMoreThenDayAgo(ApiTester $I): void
    {
        /**
         * @var User $user
         */
        $user = $I->grabFixture('user', 1);
        $I->amBearerAuthenticated($user->access_token);
        $I->sendPUT('/api/notes/5', [
            'title' => 'Updated note title',
        ]);
        $I->seeResponseCodeIs(403);
    }

    public function update(ApiTester $I): void
    {
        /**
         * @var User $user
         */
        $user = $I->grabFixture('user', 1);
        $I->amBearerAuthenticated($user->access_token);

        $title = 'Updated note title';
        $published_at = '25.05.2019 11:44:23';

        $I->sendPUT('/api/notes/6', [
            'title'        => $title,
            'published_at' => $published_at,
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'title'        => $title,
            'published_at' => strtotime($published_at),
            'user'         => [
                'id' => $user->id,
            ],
        ]);
    }

    public function deleteUnauthorized(ApiTester $I): void
    {
        $I->sendDELETE('/api/notes/1');
        $I->seeResponseCodeIs(401);
    }

    public function deleteByNotOwner(ApiTester $I): void
    {
        /**
         * @var User $user
         */
        $user = $I->grabFixture('user', 1);
        $I->amBearerAuthenticated($user->access_token);
        $I->sendDELETE('/api/notes/3');
        $I->seeResponseCodeIs(403);
    }

    public function deleteWitchCreatedMoreThenDayAgo(ApiTester $I): void
    {
        /**
         * @var User $user
         */
        $user = $I->grabFixture('user', 1);
        $I->amBearerAuthenticated($user->access_token);
        $I->sendDELETE('/api/notes/5');
        $I->seeResponseCodeIs(403);
    }

    public function delete(ApiTester $I): void
    {
        /**
         * @var User $user
         */
        $user = $I->grabFixture('user', 1);
        $I->amBearerAuthenticated($user->access_token);

        /**
         * @var Note $note
         */
        $note = $I->grabFixture('note', 5);

        $I->sendDELETE('/api/notes/6');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'title'        => $note->title,
            'published_at' => $note->published_at,
            'user'         => [
                'id' => $user->id,
            ],
        ]);
    }
}