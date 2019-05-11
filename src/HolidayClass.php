<?php
/**
 * 国民の祝日取得クラス。
 * 日本の国民の祝日を取得します。
 * @copyright (c) Takahisa Ishida <sekidenkiku@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/sekidenkiku/syukujitsu
 * @version 1.0.1
 */

namespace sekidenkiku\syukujitsu;

/**
 * 休日クラス。DateTimeクラスの継承クラス。
 */
class HolidayClass extends \DateTime
{
    /**
     * @var string 祝日・休日名。
     */
    private $holiday_name;

    /**
     * 祝日・休日名を設定します。
     * @param string $name 祝日・休日名。
     */
    public function setName(string $name): void
    {
        $this->holiday_name = $name;
    }

    /**
     * 祝日・休日名を返します。
     * @return string 祝日・休日名。
     */
    public function getName(): string
    {
        return $this->holiday_name;
    }
}