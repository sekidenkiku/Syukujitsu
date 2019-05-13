<?php
/**
 * 国民の祝日取得クラス。
 * 日本の国民の祝日を取得します。
 * @copyright (c) Takahisa Ishida <sekidenkiku@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/sekidenkiku/syukujitsu
 * @version 1.0.2
 */

namespace sekidenkiku\syukujitsu\HolidayTypes;

use sekidenkiku\syukujitsu\HolidayTypeAbstract;

/**
 * 天皇誕生日。
 */
class Tennotanjyobi extends HolidayTypeAbstract
{
    public function getName(): string
    {
        return "天皇誕生日";
    }

    /**
     * 指定された年の祝日の日付をDateTimeオブジェクトで返します。
     * @param int $year 西暦。
     * @return \DateTime|null DateTimeオブジェクト。存在しない場合は、nullを返します。
     * @throws \Exception
     */
    public function findDate(int $year): ?\DateTime
    {
        if (1949 <= $year && $year <= 1988) {
            $result = new \DateTime("{$year}-4-29", $this->getTimezone());
        } elseif (1989 <= $year && $year <= 2018) {
            $result = new \DateTime("{$year}-12-23", $this->getTimezone());
        } elseif ($year == 2019) {
            $result = null;
        } elseif (2020 <= $year) {
            $result = new \DateTime("{$year}-2-23", $this->getTimezone());
        } else {
            $result = null;
        }
        return $result;
    }
}
