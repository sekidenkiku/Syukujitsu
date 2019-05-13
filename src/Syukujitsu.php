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
class Syukujitsu
{
    /**
     * @var array 祝日作成クラスの名前一覧(HolidayTypesディレクトリ内のファイル名)
     */
    private $holiday_class_names = [
        'Bunkanohi',
        'Ganjitsu',
        'Keironohi',
        'Kenkokukinennohi',
        'Kenpokinenbi',
        'Kinrokansyanohi',
        'Kodomonohi',
        'Koutaisiakihitosinnounokekonnogi',
        'Koutaisinaruhitosinnounokekonnogi',
        'Midorinohi',
        'Seijinnohi',
        'Sokuinohi',
        'Sokuireiseidennogi',
        'Supotunohi',
        'Syowanohi',
        'Syowatennounotaisonorei',
        'Syubunnohi',
        'Syunbunnohi',
        'Taiikunohi',
        'Tennotanjyobi',
        'Uminohi',
        'Yamanohi',
    ];

    /**
     * @var \DateTimeZone タイムゾーン。
     */
    private $timezone;

    /**
     * Syukujitsu constructor.
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
     * @return \DateTimeZone タイムゾーン。
     */
    public function getTimezone(): \DateTimeZone
    {
        return $this->timezone;
    }

    /**
     * 指定された日付が休日(国民の祝日、振替休日、国民の休日)の場合、休日を返します。
     * @param string $time 日付の文字列。
     * @return HolidayClass|null 休日オブジェクト。休日でない場合nullを返します。
     * @throws \Exception
     */
    public function check(string $time): ?HolidayClass
    {
        $datetime = new \DateTime($time, $this->timezone);
        $datetime->setTime(0, 0, 0);
        $year = intval($datetime->format("Y"));
        $result = null;
        $holidays = $this->find($year);
        foreach ($holidays as $holiday) {
            if ($datetime == $holiday) {
                $result = $holiday;
                break;
            }
        }
        return $result;
    }

    /**
     * 指定された年、月の休日(国民の祝日、振替休日、国民の休日)の一覧を返します。
     * @param int $year 西暦。
     * @param int|null $month 月。省略した場合、1～12月の休日を返します。
     * @return HolidayClass[] 休日オブジェクトの配列。存在しない場合は空の配列を返します。
     * @throws \Exception
     */
    public function find(int $year, ?int $month = null): array
    {
        static $cache = [];
        $cache_name = strval($year) . $this->timezone->getName();
        if (isset($cache[$cache_name])) {
            $result = $cache[$cache_name];
        } else {
            $result = $this->findYearHoliday($year);
            // キャッシュの保存。
            $cache = $this->addCache($cache, $cache_name, $result);
        }
        // 月が指定された場合、月内の祝日を抽出。
        if (!is_null($month)) {
            $result = $this->extractMonthHoliday($result, $year, $month);
        }
        return array_values($result);
    }

    /**
     * 指定された年の国民の祝日を返します。
     * @param int $year 西暦。
     * @return HolidayClass[] 休日オブジェクトの配列。存在しない場合は空の配列を返します。
     * @throws \Exception
     */
    private function findPublicHolidays(int $year): array
    {
        static $cache = [];
        $cache_name = strval($year) . $this->timezone->getName();
        if (isset($cache[$cache_name])) {
            return $cache[$cache_name];
        } else {
            $result = [];
            // 祝日の判定
            foreach ($this->holiday_class_names as $class_name) {
                $class_name = 'sekidenkiku\syukujitsu\HolidayTypes\\' . $class_name;
                /**
                 * @var HolidayTypeAbstract $obj
                 */
                $obj = new $class_name($this->timezone);
                if (!is_null($date = $obj->findDate($year))) {
                    $holiday = new HolidayClass($date->format('Y-m-d'), $this->timezone);
                    $holiday->setName($obj->getName());
                    $result[] = $holiday;
                }
            }
            sort($result);
            // キャッシュの保存。
            $cache = $this->addCache($cache, $cache_name, $result);
            return $result;
        }
    }

    /**
     * 指定された年の振替休日を返します。
     * @param int $year 西暦。
     * @return HolidayClass[] 休日オブジェクトの配列。存在しない場合は空の配列を返します。
     * @throws \Exception
     */
    private function findHurikaekyujitu(int $year): array
    {
        // @TODO 年をまたぐ振替休日に対応していない。将来12月31日が祝日になった場合は修正する。
        // ①法改正(1973年4月12日施行)で「２　「国民の祝日」が日曜日にあたるときは、その翌日を休日とする。」が追加された。
        // ②法改正(2007年1月1日施行)で「２　「国民の祝日」が日曜日に当たるときは、その日後においてその日に最も近い「国民の祝日」でない日を休日とする。」に変更された。
        // 過去のカレンダーから結果的に②の条件で①も満たしているため、②の条件だけで①②を兼ねる。
        $result = [];
        $substitute_holidays = $this->findSubstituteHoliday($this->findPublicHolidays($year));
        $begin = new \DateTime("1973-4-12", $this->timezone);
        foreach ($substitute_holidays as $value) {
            // 振替休日は、1973年4月12日施行。
            if ($value >= $begin) {
                $obj = new HolidayClass($value->format('Y-m-d'), $this->timezone);
                $obj->setName('振替休日');
                $result[] = $obj;
            }
        }
        return $result;
    }

    /**
     * 指定された年の国民の休日を返します。
     * @param int $year 西暦。
     * @return HolidayClass[] 休日オブジェクトの配列。存在しない場合は空の配列を返します。
     * @throws \Exception
     */
    private function findKokuminnokyujitu(int $year): array
    {
        // @TODO 年をまたぐ国民の休日に対応していない。将来12月30日が祝日となり1月1日(祝日)と挟まれた場合は修正する。
        $result = [];
        $sandwiched_days = $this->findPublicHolidaySandwichedDay($this->findPublicHolidays($year));
        $begin = new \DateTime("1985-12-27", $this->timezone);
        $secound_begin = new \DateTime("2006-12-31", $this->timezone);
        $hurikaekyujitus = $this->findHurikaekyujitu($year);
        foreach ($sandwiched_days as $value) {
            // 法改正(1985年12月27日施行)で「３　その前日及び翌日が「国民の祝日」である日（日曜日にあたる日及び前項に規定する休日にあたる日を除く。）は、休日とする。」が追加された。
            // 法改正(2007年1月1日施行)で「３　その前日及び翌日が「国民の祝日」である日（「国民の祝日」でない日に限る。）は、休日とする。」に変更された。
            // 簡単に説明すると、2006年12月31日以前は、日曜または振替休日に当たる場合は「国民の休日」にならない。2007年以降は、国民の祝日の場合は「国民の休日」にならない。
            if ($value >= $begin) {
                if ($value <= $secound_begin) {
                    // 2006年12月31日以前は、国民の休日が日曜または振替休日の場合は「国民の休日」にならない処理。
                    $able = true;
                    if (0 == $value->format('w')) {
                        // 日曜日の場合。
                        $able = false;
                    } else {
                        // 振替休日の場合。
                        foreach ($hurikaekyujitus as $hurikaekyujitu) {
                            if ($hurikaekyujitu == $value) {
                                $able = false;
                                break;
                            }
                        }
                    }
                    if (false === $able) {
                        continue;
                    }
                }
                $obj = new HolidayClass($value->format('Y-m-d'), $this->timezone);
                $obj->setName('国民の休日');
                $result[] = $obj;
            }
        }
        return $result;
    }

    /**
     * 日曜日の休日オブジェクトの次の休日オブジェクトでない日を返します。
     * @param HolidayClass[] $holidays 休日オブジェクトの配列。
     * @return HolidayClass[] 休日オブジェクトの配列。存在しない場合は空の配列を返します。
     * @throws \Exception
     */
    private function findSubstituteHoliday(array $holidays): array
    {
        $result = [];
        foreach ($holidays as $holiday) {
            if ("0" === $holiday->format('w')) {
                $date2 = clone $holiday;
                do {
                    $date2->modify('+1 days');
                } while ($this->isDuplicated($date2, $holidays));
                $result[] = $date2;
            }
        }
        return $result;
    }

    /**
     * 前後を休日オブジェクトで挟まれた日を返します。
     * @param HolidayClass[] $holidays 休日オブジェクトの配列。
     * @return HolidayClass[] 休日オブジェクトの配列。存在しない場合は空の配列を返します。
     * @throws \Exception
     */
    private function findPublicHolidaySandwichedDay(array $holidays): array
    {
        $result = [];
        foreach ($holidays as $key => $holiday) {
            $date2 = clone $holiday;
            $date2->modify('+1 days');
            $date3 = clone $holiday;
            $date3->modify('+2 days');
            if (true === $this->isDuplicated($date3, $holidays) && false === $this->isDuplicated($date2, $holidays)) {
                $result[] = $date2;
            }
        }
        return $result;
    }

    /**
     * 重複した日付が存在する場合trueを返します。
     * @param \DateTime $search 検索する日付。
     * @param HolidayClass[] $holidays 休日オブジェクトの配列。
     * @return bool 日付が一致するクラスが見つかった場合trueを返します。
     */
    private function isDuplicated(\DateTime $search, array $holidays): bool
    {
        foreach ($holidays as $holiday) {
            if ($search == $holiday) {
                return true;
            }
        }
        return false;
    }

    /**
     * キャッシュ変数にキャッシュを追加して返します。
     * @param array $buff キャッシュ変数。
     * @param string $name キャッシュ名。
     * @param array $data データ。
     * @return array キャッシュ変数。
     */
    private function addCache(array $buff, string $name, array $data): array
    {
        // キャッシュの保存。
        $buff[$name] = $data;
        // キャッシュのサイズ制限。古いキャッシュ(配列先頭の値)を削除。※array_shift()で削除すると処理が遅くなるため、unset()を使用している。
        if (count($buff) > 10) {
            reset($buff);
            unset($buff[key($buff)]);
        }
        return $buff;
    }

    /**
     * 指定された年の休日(祝日、振替休日、国民の休日)を返します。
     * @param int $year 西暦。
     * @return HolidayClass[] 祝日クラスのインスタントを配列で返します。
     * @throws \Exception
     */
    private function findYearHoliday(int $year): array
    {
        $holidays = $this->findPublicHolidays($year);
        // 振替休日の取得。(独自ルールとして、振替休日と国民の休日の両方の条件を満たす場合は振替休日とします。そのため国民の休日より先に追加する)
        $holidays = array_merge($holidays, $this->findHurikaekyujitu($year));

        // 国民の休日取得。
        foreach ($this->findKokuminnokyujitu($year) as $val) {
            if (false === $this->isDuplicated($val, $holidays)) {
                $holidays[] = $val;
            }
        }
        sort($holidays);
        return $holidays;
    }

    /**
     * 指定された月内の祝日を抽出して返します。
     * @param HolidayClass[] $holidays 祝日クラス。
     * @param int $year 西暦。
     * @param int $month 月。
     * @return HolidayClass[] 祝日クラスのインスタントを配列で返します。
     * @throws \Exception
     */
    private function extractMonthHoliday(array $holidays, int $year, int $month): array
    {
        if (1 <= $month && $month <= 12) {
            $first = new \DateTime("{$year}-{$month}-1", $this->timezone);
            $last = new \DateTime("last day of {$year}-{$month}", $this->timezone);
            /**
             * @var $holiday HolidayClass 祝日オブジェクト
             */
            foreach ($holidays as $key => $holiday) {
                if ($first > $holiday || $last < $holiday) {
                    unset($holidays[$key]);
                }
            }
            $result = $holidays;
        } else {
            $result = [];
        }
        return $result;
    }
}