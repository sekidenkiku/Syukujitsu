<?php
/**
 * 国民の祝日取得クラス。
 * 日本の国民の祝日を取得します。
 * @copyright (c) Takahisa Ishida <sekidenkiku@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/sekidenkiku/syukujitsu
 * @version 1.0.2
 */

namespace sekidenkiku\syukujitsu;

/**
 * 祝日拡張クラス。
 */
abstract class HolidayTypeAbstract
{
    /**
     * @var \DateTimeZone タイムゾーン。
     */
    private $timezone;

    /**
     * HolidayTypeAbstract constructor.
     * @param \DateTimeZone|null $timezone タイムゾーンオブジェクト。指定しない場合はデフォルトのタイムゾーンになります。
     */
    public function __construct(\DateTimeZone $timezone = null)
    {
        if ($timezone instanceof \DateTimeZone) {
            $this->timezone = $timezone;
        } else {
            $str = date_default_timezone_get();
            $this->timezone = new \DateTimeZone($str);
        }
    }

    /**
     * クラス内のタイムゾーンを設定します。
     * @param \DateTimeZone $timezone タイムゾーン。
     */
    public function setTimezone(\DateTimeZone $timezone): void
    {
        $this->timezone = $timezone;
    }

    /**
     * クラス内のタイムゾーンを返します。
     * @return \DateTimeZone タイムゾーン
     */
    public function getTimezone(): \DateTimeZone
    {
        return $this->timezone;
    }

    /**
     * 祝日名を返します。
     * @return string
     */
    abstract public function getName(): string;

    /**
     * 指定された年の祝日の日付をDateTimeオブジェクトで返します。
     * @param int $year 西暦。
     * @return \DateTime|null DateTimeオブジェクト。存在しない場合は、nullを返します。
     * @throws \Exception
     */
    abstract public function findDate(int $year): ?\DateTime;
}