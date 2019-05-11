<?php
/**
 * 国民の祝日取得クラス。
 * 日本の国民の祝日を取得します。
 * @copyright (c) Takahisa Ishida <sekidenkiku@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/sekidenkiku/syukujitsu
 * @version 1.0.1
 */

namespace sekidenkiku\syukujitsu\HolidayTypes;

use sekidenkiku\syukujitsu\HolidayTypeAbstract;

/**
 * スポーツの日。
 */
class Supotunohi extends HolidayTypeAbstract
{
    public function getName(): string
    {
        return 'スポーツの日';
    }

    /**
     * 指定された年の祝日の日付をDateTimeオブジェクトで返します。
     * @param int $year 西暦。
     * @return \DateTime|null DateTimeオブジェクト。存在しない場合は、nullを返します。
     * @throws \Exception
     */
    public function getDate(int $year): ?\DateTime
    {
        if (2020 == $year) {
            $result = new \DateTime("{$year}-7-24", $this->getTimezone());
        } elseif (2021 <= $year) {
            $result = new \DateTime("second mon of October {$year}", $this->getTimezone());
        } else {
            $result = null;
        }
        return $result;
    }
}
