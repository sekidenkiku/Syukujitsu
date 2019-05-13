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
 * 皇太子明仁親王の結婚の儀
 */
class Koutaisiakihitosinnounokekonnogi extends HolidayTypeAbstract
{
    public function getName(): string
    {
        return "皇太子明仁親王の結婚の儀";
    }

    /**
     * 指定された年の祝日の日付をDateTimeオブジェクトで返します。
     * @param int $year 西暦。
     * @return \DateTime|null DateTimeオブジェクト。存在しない場合は、nullを返します。
     * @throws \Exception
     */
    public function findDate(int $year): ?\DateTime
    {
        if (1959 == $year) {
            $result = new \DateTime("1959-4-10", $this->getTimezone());
        } else {
            $result = null;
        }
        return $result;
    }
}
