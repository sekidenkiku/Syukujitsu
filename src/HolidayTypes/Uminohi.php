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
 * 海の日。
 */
class Uminohi extends HolidayTypeAbstract
{
    public function getName(): string
    {
        return "海の日";
    }

    /**
     * 指定された年の祝日の日付をDateTimeオブジェクトで返します。
     * @param int $year 西暦。
     * @return \DateTime|null DateTimeオブジェクト。存在しない場合は、nullを返します。
     * @throws \Exception
     */
    public function findDate(int $year): ?\DateTime
    {
        if (1996 <= $year && $year <= 2002) {
            $result = new \DateTime("{$year}-7-20", $this->getTimezone());
        } elseif (2003 <= $year && $year <= 2019) {
            $result = new \DateTime("third mon of July {$year}", $this->getTimezone());
        } elseif (2020 == $year) {
            $result = new \DateTime("{$year}-7-23", $this->getTimezone());
        } elseif (2021 == $year) {
            $result = new \DateTime("{$year}-7-22", $this->getTimezone());
        } elseif (2022 <= $year) {
            $result = new \DateTime("third mon of July {$year}", $this->getTimezone());
        } else {
            $result = null;
        }
        return $result;
    }
}
