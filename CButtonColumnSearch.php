<?php
/**
 * Колонка с операциями и кнопкой поиска сверху
 */
class CButtonColumnSearch extends CButtonColumn
{

    /**
     * Фильтр с кнопкой поиска
     * @return void
     */
    protected function renderFilterCellContent()
    {
        // TODO: Нормально реализовать!
        echo GxHtml::button('Поиск');
    }
}
