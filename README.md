Yii-x набор компонент для Yii
=============================

## Установка

* git submodule add git@github.com:stden/yii-x.git <application>/protected/extensions/yii-x.
* Добавить следующие строки в файл конфигурации `protected/config/main.php`:

```php
<?php
...
	'import'=>array(
        // Компоненты yii-x
        'ext.yii-x.*',
	),
...
...
```

## Использование

#### Работа с днями рождений

```php
<?php
...
<?php

require_once dirname(__FILE__) . '/../bootstrap.php';

class BirthdayTest extends CDbTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Добавление дня рождения
     * @return void
     */
    public function testAddBirthday()
    {
        $b = new DateUtils();
        $b->today = new DateTime();

        $d = clone $b->today;
        $origin = clone $d; // Сохраняем исходное значение
        $this->assertEquals($d, $b->NextTime($d));

        $d->sub(new DateInterval("P1D")); // Событие было вчера
        // Следующий раз оно будет через год минус 1 день
        $n = clone $origin;
        $n->add(new DateInterval("P1Y"));
        $n->sub(new DateInterval("P1D"));
        $d_origin = clone $d;
        $this->assertEquals($n, $b->NextTime($d));
        $this->assertEquals($d_origin, $d);

        $t1 = new DateTime();
        $t2 = new DateTime();
        $t = $t1->diff($t2);
        echo $t->format('%R%');

        Yii::app()->language = 'ru';
        $r = $b->calc($b->today);
        $this->assertEquals(array('days' => 0, 'm' => 0, 'd' => 0, 'diff' => 'Сегодня!'), $r);

        Yii::app()->language = 'en';
        $r = $b->calc($b->today);
        $this->assertEquals(array('days' => 0, 'm' => 0, 'd' => 0, 'diff' => 'Today!'), $r);
    }

    public function testReminderMessage()
    {
        $tests = array(
            1 => 'Завтра',
            2 => '1 день',
            3 => '2 дня',
            4 => '3 дня',
            5 => '4 дня',
            6 => '5 дней'
        );

        Yii::app()->language = 'ru';
        foreach ($tests as $days => $str) {
            $b = new DateUtils();
            $d = clone $b->today;
            $d->add(new DateInterval("P{$days}D"));
            $r = $b->calc($d);
            $this->assertEquals(array('days' => $days, 'm' => 0, 'd' => $days, 'diff' => $str), $r);
        }
    }

}
```
