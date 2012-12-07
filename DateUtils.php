<?php
/**
 * Work with dates and birthdays
 */
class DateUtils
{
    /**
     * @var DateTime От какой даты считаем (по-умолчанию сегодняшний день)
     */
    public $today;

    /**
     * Заполняем начальные значения
     */
    public function __construct()
    {
        $this->today = new DateTime();
    }

    /**
     * @param $birthday
     * @return array
     */
    public function calc(DateTime $birthday)
    {
        $date2 = $this->NextTime($birthday);
        $interval = $this->today->diff($date2);
        $r['days'] = DateUtils::daysDiff($this->today, $date2);
        $r['m'] = $interval->m;
        $r['d'] = $interval->d;
        if ($interval->m == 0) {
            $dd = $interval->d - 1;
            switch ($interval->d) {
                case 0:
                    $r['diff'] = Yii::t('DateUtils.datetime', 'Today!');
                    break;
                case 1:
                    $r['diff'] = Yii::t('DateUtils.datetime', 'Tomorrow');
                    break;
                default:
                    $r['diff'] = $this->endings($dd);
            }
        }
        else
            $r['diff'] = self::endings($interval->m, 'month') . " " . self::endings($interval->d);

        return $r;
    }

    /**
     * Правильные окончания единиц после числительных
     * @static
     * @param $number Количество
     * @param string $h Базовая единица измерения на английском языке
     * @return string Строка с переводом + правильными окончаниями числительных.
     */
    public static function endings($number, $h = 'day')
    {
        return $number . ' ' . Yii::t('DateUtils.datetime', $h, [$number]);
    }

    /**
     * @static Когда День Рождения будет в следующий раз?
     * @param $birthday DateTime
     * @return \DateTime
     */
    public function NextTime(DateTime $birthday)
    {
        $b = clone $birthday; // clone обязателен!
        //$b->add(new DateInterval("P1D")); // Вычитаем день
        //$b->sub(new DateInterval("PT1S")); // Добавляем секунду
        while ($b < $this->today) {
            $b->add(new DateInterval('P1Y'));
        }
        return $b;
    }

    /**
     * Разница в днях
     * @static
     * @param $dt1 От даты
     * @param $dt2 До даты
     * @return int Количество дней
     */
    public static function daysDiff($dt1, $dt2)
    {
        if (is_string($dt1)) {
            $dt1 = new DateTime($dt1);
        }
        if (is_string($dt2)) {
            $dt2 = new DateTime($dt2);
        }
        // else let's use our own method
        $y1 = $dt1->format('Y');
        $y2 = $dt2->format('Y');
        $z1 = $dt1->format('z');
        $z2 = $dt2->format('z');

        $diff = intval($y2 * 365.2425 + $z2) - intval($y1 * 365.2425 + $z1);
        return $diff;
    }

    /**
     * @static Сколько осталось времени?
     * @param DateInterval $diff
     * @return string Строка с оставшимся временем
     */
    public static function timeLeft(DateInterval $diff)
    {
        // При указании времени хранения файла нужно указывать часы только в случае если осталось менее 1 дня.
        // Если 1 день и более, то нужно указывать количество дней. Так будет проще восприниматься.
        // Если осталось больше одного дня => выводим только количество дней
        if ($diff->d >= 1)
            return $diff->format('%d ' . Yii::t('DateUtils.datetime', 'day', [$diff->d]));
        // Если больше часа - выводим часы и минуты
        if ($diff->h >= 1)
            return $diff->format('%h ' . Yii::t('DateUtils.datetime', 'hour', [$diff->h]) . ' %I ' .
                Yii::t('DateUtils.datetime', 'minute', [$diff->m]));
        // Если меньше часа - выводим минуты и секунды
        return $diff->format('%i ' . Yii::t('DateUtils.datetime', 'minute', [$diff->m]) .
            " %s " . Yii::t('DateUtils.datetime', 'second', [$diff->s]));
    }
}

