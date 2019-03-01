<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Notes REST API</h1>
</p>

USED
------------

- Postgres 10

- PHP 7.1

IN PROJECT
------------
1 - Composer install

2 - Apply migrations
~~~
./yii migrate
~~~

3 - Seed database for testing API
~~~
./yii seed
~~~

TESTS
------------

1 - Connect to tests db in /config/test_db.php

2 - Apply migrations for tests db (**if you use a separate db**)

~~~
tests/bin/yii migrate
~~~

3 - Up server

~~~
./yii serve
~~~

4 - Build tester

~~~
vendor/bin/codecept build
~~~

5 - Run

~~~
vendor/bin/codecept run api
~~~

Example results:
~~~
Api Tests (16) ------------------------------------------------------------------------------------------------------------------------------------------------------------------
✔ NotesCest: Index (0.13s)
✔ NotesCest: Index with page (0.09s)
✔ NotesCest: Index with invalid page (0.06s)
✔ NotesCest: View (0.09s)
✔ NotesCest: View has been deleted (0.07s)
✔ NotesCest: View with future publication date (0.06s)
✔ NotesCest: Create unauthorized (0.00s)
✔ NotesCest: Create (0.09s)
✔ NotesCest: Update unauthorized (0.00s)
✔ NotesCest: Update by not owner (0.08s)
✔ NotesCest: Update witch created more then day ago (0.08s)
✔ NotesCest: Update (0.08s)
✔ NotesCest: Delete unauthorized (0.00s)
✔ NotesCest: Delete by not owner (0.08s)
✔ NotesCest: Delete witch created more then day ago (0.08s)
✔ NotesCest: Delete (0.10s)
~~~