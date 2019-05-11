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
 * 秋分の日
 */
class Syubunnohi extends HolidayTypeAbstract
{
    public function getName(): string
    {
        return "秋分の日";
    }

    /**
     * 指定された年の祝日の日付をDateTimeオブジェクトで返します。
     * @param int $year 西暦。
     * @return \DateTime|null DateTimeオブジェクト。存在しない場合は、nullを返します。
     * @throws \Exception
     */
    public function getDate(int $year): ?\DateTime
    {
        if (1948 <= $year) {
            if (1851 <= $year && $year <= 1899) {
                // 1851-1899年
                $day = intval(22.2588 + 0.242194 * ($year - 1980) - intval(($year - 1983) / 4));

            } elseif (1900 <= $year && $year <= 1979) {
                // 1900-1979年
                $day = intval(23.2588 + 0.242194 * ($year - 1980) - intval(($year - 1983) / 4));

            } elseif (1980 <= $year && $year <= 2099) {
                // 1980-2099年
                $day = intval(23.2488 + 0.242194 * ($year - 1980) - intval(($year - 1980) / 4));

            } elseif (2100 <= $year && $year <= 2150) {
                //  2100-2150年
                $day = intval(24.2488 + 0.242194 * ($year - 1980) - intval(($year - 1980) / 4));
            } else {
                return null;
            }
        } else {
            return null;
        }
        return new \DateTime("{$year}-9-{$day}", $this->getTimezone());
    }
}
