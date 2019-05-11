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
 * 成人の日。
 */
class Seijinnohi extends HolidayTypeAbstract
{
    public function getName(): string
    {
        return '成人の日';
    }

    /**
     * 指定された年の祝日の日付をDateTimeオブジェクトで返します。
     * @param int $year 西暦。
     * @return \DateTime|null DateTimeオブジェクト。存在しない場合は、nullを返します。
     * @throws \Exception
     */
    public function getDate(int $year): ?\DateTime
    {
        if (1949 <= $year AND $year <= 1999) {
            $result = new \DateTime("{$year}-1-15", $this->getTimezone());
        } elseif (2000 <= $year) {
            $result = new \DateTime("second mon of January {$year}", $this->getTimezone());
        } else {
            $result = null;
        }
        return $result;
    }
}
