<?php
/**
 * Форматирование размера файла
 */
class FileSize
{
    /**
     * @static
     * @param $size_in_bytes
     * @return string
     */
    public static function SizeStr($size_in_bytes)
    {
        $KB = 1 << 10; // Килобайт
        $MB = 1 << 20; // Мегабайт
        $GB = 1073741824; // Гигабайт
        $TB = 1099511627776; // Терабайт
        $PB = 1125899906842624; // Петабайт
        $EB = 1152921504606846976; // Экзабайт
        $ZB = 1180591620717411303424;
        $YB = 1208925819614629174706176;
        if ($size_in_bytes < $KB) {
            return $size_in_bytes . ' ' . Yii::t('FileSize.filesize', 'B');
        } elseif ($size_in_bytes < $MB) {
            return round($size_in_bytes / $KB, 2) . ' ' . Yii::t('FileSize.filesize', 'KiB');
        } elseif ($size_in_bytes < $GB) {
            return round($size_in_bytes / $MB, 2) . ' ' . Yii::t('FileSize.filesize', 'MiB');
        } elseif ($size_in_bytes < $TB) {
            return round($size_in_bytes / $GB, 2) . ' ' . Yii::t('FileSize.filesize', 'GiB');
        } elseif ($size_in_bytes < $PB) {
            return round($size_in_bytes / $TB, 2) . ' ' . Yii::t('FileSize.filesize', 'TiB');
        } elseif ($size_in_bytes < $EB) {
            return round($size_in_bytes / $PB, 2) . ' ' . Yii::t('FileSize.filesize', 'PiB');
        } elseif ($size_in_bytes < $ZB) {
            return round($size_in_bytes / $EB, 2) . ' ' . Yii::t('FileSize.filesize', 'EiB');
        } elseif ($size_in_bytes < $YB) {
            return round($size_in_bytes / $ZB, 2) . ' ' . Yii::t('FileSize.filesize', 'ZiB');
        } else {
            return round($size_in_bytes / $YB, 2) . ' ' . Yii::t('FileSize.filesize', 'YiB');
        }
    }
}
