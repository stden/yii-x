<?php
/**
 * Расширенная дата и время
 * В этом классе конструктор не перегружается, испольуется конструктор DateTime, который инициализирует объект текущей
 * датой и временем. Для тестов можно вызывать конструктор с тестовыми датой и временем, например, так:
 * new XDateTime('2011-11-10')
 */
class XDateTime extends DateTime
{
    /**
     * @return string Месяц по-русски
     */
    public function month()
    {
        $m = ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'];
        return $m[intVal($this->format('m')) - 1];
    }

    /**
     * @return string Дата в "русском" формате
     */
    public function date()
    {
        return $this->format('d.m.Y');
    }

    /**
     * @return string Дата и время в "русском" формате
     */
    public function date_time()
    {
        return $this->format('d.m.Y G:i:s');
    }

    /**
     * @return string Начало месяца в формате MySQL
     */
    public function month_begin()
    {
        return '01.' . $this->format('m.Y') . ' 00:00:00'; // Начало суток
    }

    /**
     * @return string Конец дня в формате MySQL
     */
    public function day_end()
    {
        return $this->format('d.m.Y') . ' 23:59:59'; // Последняя секунда суток
    }

    /**
     * @return string На какое число будет заявка?
     */
    public function request()
    {
        if ($this->day() < 10) // Если число до 10, тогла заявка 10 числа того же месяца
            return $this->format('10.m.Y');
        if ($this->day() < 20) // с 10 по 19 число каждого месяца
            return $this->format('20.m.Y');
        else {
            $d = clone $this; // Нужно чтобы не менять текущую дату!
            return $d->add(new DateInterval('P1M'))->format('10.m.Y');
        }
    }

    /**
     * @return int День
     */
    public function day()
    {
        return intVal($this->format('d'));
    }

    /**
     * @return string Дата и время в формате MySQL
     */
    public function MySQL()
    {
        return $this->format('Y-m-d G:i:s');
    }

    /**
     * @return string Только дата в формате MySQL
     */
    public function MySQL_Date()
    {
        return $this->format('Y-m-d');
    }
}
